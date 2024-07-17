<?php

namespace App\Service\Jwt;

use App\Entity\Account\Account;
use App\Entity\Account\ApiToken;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtExpiration;
use App\Helper\Jwt\JwtUsage;
use App\Repository\Account\ApiTokenRepository;
use App\Service\Account\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtService
{
    /**
     * @var JWTEncoderInterface $jwtManager
     */
    private JWTEncoderInterface $jwtEncoder;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $em;

    /**
     * @var ApiTokenRepository $apiTokenRepository
     */
    private ApiTokenRepository $apiTokenRepository;

    /**
     * @var SessionService $sessionService
     */
    private SessionService $sessionService;

    /**
     * @param JWTEncoderInterface $jwtEncoder
     * @param EntityManagerInterface $em
     * @param ApiTokenRepository $apiTokenRepository
     * @param SessionService $sessionService
     */
    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        EntityManagerInterface $em,
        ApiTokenRepository $apiTokenRepository,
        SessionService $sessionService
    )
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
        $this->apiTokenRepository = $apiTokenRepository;
        $this->sessionService = $sessionService;
    }

    /**
     * Generate a token for a user based on the usage
     * @param UserInterface $user
     * @param JwtUsage $usage
     * @param array $data
     * @return string
     * @throws InternalException
     */
    public function generateToken(UserInterface $user, JwtUsage $usage, array $data = []): string
    {
        try {
            if (!$user instanceof Account) {
                throw new InvalidArgumentException('Expected an instance of ' . Account::class);
            }

            $expiration = $this->getExpirationForUsage($usage);
            $payload = [
                'user_id' => $user->getId(),
                'usage' => $usage->value,
                'exp' => (time() + $expiration),
            ];

            $payload = array_merge($payload, $data);

            return $this->jwtEncoder->encode($payload);
        } catch (JWTEncodeFailureException) {
            throw new InternalException('Failed to generate token.');
        }
    }

    /**
     * Create or update a refresh token for a user.
     * @param UserInterface $user
     * @param Request $request
     * @return ApiToken
     * @throws InternalException
     */
    public function createOrUpdateRefreshToken(UserInterface $user, Request $request): ApiToken
    {
        if (!$user instanceof Account) {
            throw new InvalidArgumentException('Expected an instance of ' . Account::class);
        }

        $sessionId = $this->sessionService->generate($request);
        $existingToken = $this->apiTokenRepository->findByUserAndSession($user, $sessionId);

        if ($existingToken) {
            $existingToken->updateToken($this->generateToken($user, JwtUsage::USAGE_API_REFRESH));
            $this->em->flush();
            return $existingToken;
        }

        $refreshToken = new ApiToken(
            $this->generateToken($user, JwtUsage::USAGE_API_REFRESH),
            $sessionId,
            $user
        );
        $this->em->persist($refreshToken);
        $this->em->flush();

        return $refreshToken;
    }

    /**
     * Decode a JWT token
     * @param string $token
     * @param JwtUsage $usage
     * @return array
     * @throws UnauthorizedException
     */
    public function decodeToken(string $token, JwtUsage $usage): array
    {
        $invalidTokenMessage = 'Invalid token.';

        try {
            $decodedToken = $this->jwtEncoder->decode($token);

            if ($decodedToken['usage'] !== $usage->value) {
                throw new UnauthorizedException($invalidTokenMessage);
            }

        } catch (JWTDecodeFailureException) {
            throw new UnauthorizedException($invalidTokenMessage);
        }

        return $decodedToken;
    }

    /**
     * @param $refresh_token
     * @return string
     * @throws BadRequestException
     * @throws InternalException
     * @throws UnauthorizedException
     */
    public function refreshToken($refresh_token): string
    {
        /** @var ApiToken $apiToken */
        $apiToken = $this->apiTokenRepository->findOneBy(['refreshToken' => $refresh_token]);

        if (!$apiToken) {
            throw new BadRequestException('Invalid refresh token.');
        }

        // Decode to check if the token is valid
        $this->decodeToken($refresh_token, JwtUsage::USAGE_API_REFRESH);

        return $this->generateToken($apiToken->getAccount(), JwtUsage::USAGE_API_ACCESS);
    }

    /**
     * @param JwtUsage $usage
     * @return int
     */
    private function getExpirationForUsage(JwtUsage $usage): int
    {
        return match($usage) {
            JwtUsage::USAGE_API_ACCESS => JwtExpiration::EXPIRATION_10_MINUTES->value,
            JwtUsage::USAGE_API_REFRESH => JwtExpiration::EXPIRATION_1_YEAR->value,
            JwtUsage::USAGE_ACCOUNT_ACTIVATION, JwtUsage::USAGE_PASSWORD_RESET => JwtExpiration::EXPIRATION_1_HOUR->value,
            JwtUsage::USAGE_UPLOAD => JwtExpiration::EXPIRATION_1_WEEK->value,
            JwtUsage::USAGE_VIDEO_ACCESS => JwtExpiration::EXPIRATION_1_DAY->value,
        };
    }
}
