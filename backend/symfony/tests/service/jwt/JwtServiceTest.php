<?php

namespace App\Tests\Service\Jwt;

use App\Entity\Account\ApiToken;
use App\Entity\Account\Account;
use App\Helper\Api\Exception\BadRequestException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Jwt\JwtUsage;
use App\Repository\Account\ApiTokenRepository;
use App\Service\Account\SessionService;
use App\Service\Jwt\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JwtServiceTest extends TestCase
{
    private const TEST_USER_ID = 1;
    private const TEST_TOKEN = 'valid_token';
    private const TEST_SESSION_ID = 'session_id';
    private const TEST_REFRESH_TOKEN = 'refresh_token';

    private $jwtEncoder;
    private $em;
    private $apiTokenRepository;
    private $sessionService;
    private $jwtService;
    private $account;

    protected function setUp(): void
    {
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->apiTokenRepository = $this->createMock(ApiTokenRepository::class);
        $this->sessionService = $this->createMock(SessionService::class);
        $this->jwtService = new JwtService(
            $this->jwtEncoder,
            $this->em,
            $this->apiTokenRepository,
            $this->sessionService
        );

        $this->account = new Account('test@example.com', 'password', 'salt');
        $this->account->setId(self::TEST_USER_ID);
    }

    public function testGenerateTokenSuccess()
    {
        $this->jwtEncoder->expects($this->once())
            ->method('encode')
            ->with($this->callback(function($payload) {
                return $payload['user_id'] === self::TEST_USER_ID &&
                    $payload['usage'] === JwtUsage::USAGE_API_ACCESS->value;
            }))
            ->willReturn(self::TEST_TOKEN);

        $token = $this->jwtService->generateToken($this->account, JwtUsage::USAGE_API_ACCESS);

        $this->assertEquals(self::TEST_TOKEN, $token);
    }

    public function testGenerateTokenFailure()
    {
        $this->jwtEncoder->expects($this->once())
            ->method('encode')
            ->willThrowException(new JWTEncodeFailureException('encode_failure', 0));

        $this->expectException(InternalException::class);

        $this->jwtService->generateToken($this->account, JwtUsage::USAGE_API_ACCESS);
    }

    public function testCreateOrUpdateRefreshTokenCreatesNewToken()
    {
        $request = $this->createMock(Request::class);

        $this->sessionService->expects($this->once())
            ->method('generate')
            ->with($request)
            ->willReturn(self::TEST_SESSION_ID);

        $this->apiTokenRepository->expects($this->once())
            ->method('findByUserAndSession')
            ->with($this->account, self::TEST_SESSION_ID)
            ->willReturn(null);

        $this->jwtEncoder->expects($this->once())
            ->method('encode')
            ->willReturn(self::TEST_REFRESH_TOKEN);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(ApiToken::class));

        $this->em->expects($this->once())
            ->method('flush');

        $token = $this->jwtService->createOrUpdateRefreshToken($this->account, $request);

        $this->assertInstanceOf(ApiToken::class, $token);
        $this->assertEquals(self::TEST_REFRESH_TOKEN, $token->getRefreshToken());
    }

    public function testCreateOrUpdateRefreshTokenUpdatesExistingToken()
    {
        $request = $this->createMock(Request::class);
        $existingToken = new ApiToken(self::TEST_REFRESH_TOKEN, self::TEST_SESSION_ID, $this->account);

        $this->sessionService->expects($this->once())
            ->method('generate')
            ->with($request)
            ->willReturn(self::TEST_SESSION_ID);

        $this->apiTokenRepository->expects($this->once())
            ->method('findByUserAndSession')
            ->with($this->account, self::TEST_SESSION_ID)
            ->willReturn($existingToken);

        $this->jwtEncoder->expects($this->once())
            ->method('encode')
            ->willReturn(self::TEST_REFRESH_TOKEN);

        $this->em->expects($this->once())
            ->method('flush');

        $token = $this->jwtService->createOrUpdateRefreshToken($this->account, $request);

        $this->assertInstanceOf(ApiToken::class, $token);
        $this->assertEquals(self::TEST_REFRESH_TOKEN, $token->getRefreshToken());
    }

    public function testDecodeTokenSuccess()
    {
        $this->jwtEncoder->expects($this->once())
            ->method('decode')
            ->with(self::TEST_TOKEN)
            ->willReturn([
                'user_id' => self::TEST_USER_ID,
                'usage' => JwtUsage::USAGE_API_ACCESS->value
            ]);

        $decodedToken = $this->jwtService->decodeToken(self::TEST_TOKEN, JwtUsage::USAGE_API_ACCESS);

        $this->assertEquals(self::TEST_USER_ID, $decodedToken['user_id']);
        $this->assertEquals(JwtUsage::USAGE_API_ACCESS->value, $decodedToken['usage']);
    }

    public function testDecodeTokenInvalidUsage()
    {
        $this->jwtEncoder->expects($this->once())
            ->method('decode')
            ->with(self::TEST_TOKEN)
            ->willReturn([
                'user_id' => self::TEST_USER_ID,
                'usage' => 'invalid_usage'
            ]);

        $this->expectException(BadRequestException::class);

        $this->jwtService->decodeToken(self::TEST_TOKEN, JwtUsage::USAGE_API_ACCESS);
    }

    public function testDecodeTokenFailure()
    {
        $this->jwtEncoder->expects($this->once())
            ->method('decode')
            ->willThrowException(new JWTDecodeFailureException('decode_failure', 0));

        $this->expectException(BadRequestException::class);

        $this->jwtService->decodeToken(self::TEST_TOKEN, JwtUsage::USAGE_API_ACCESS);
    }
}
