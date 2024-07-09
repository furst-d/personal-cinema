<?php

namespace App\Tests\Controller\v1;

use App\Entity\Account\Account;
use App\Entity\Account\ApiToken;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_PASSWORD = 'password';
    private const LOGIN_URL = '/v1/users/login';
    private const REGISTER_URL = '/v1/users/register';
    private const APPLICATION_JSON = ['CONTENT_TYPE' => 'application/json'];

    private $client;
    private $mockAccountService;
    private $mockJwtService;
    private $mockMailerService;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->mockAccountService = $this->createMock(AccountService::class);
        $this->mockJwtService = $this->createMock(JwtService::class);
        $this->mockMailerService = $this->createMock(MailerService::class);

        $this->client->getContainer()->set(AccountService::class, $this->mockAccountService);
        $this->client->getContainer()->set(JwtService::class, $this->mockJwtService);
        $this->client->getContainer()->set(MailerService::class, $this->mockMailerService);
    }

    public function testRegisterSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, 'salt');
        $this->mockAccountService->method('registerUser')->willReturn($account);

        $this->mockJwtService->method('generateToken')->willReturn('activationToken');
        $this->mockMailerService->expects($this->once())->method('sendAccountActivation');

        $this->client->request('POST', self::REGISTER_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('User registered successfully', $this->client->getResponse()->getContent());
    }

    public function testRegisterUserAlreadyExists()
    {
        $this->mockAccountService->method('registerUser')->willThrowException(new ConflictException('Account already exists.'));

        $this->client->request('POST', self::REGISTER_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD
        ]));

        $this->assertEquals(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, 'salt');
        $this->mockAccountService->method('loginUser')->willReturn($account);

        $apiToken = new ApiToken('refreshToken', 'sessionId', $account);

        $this->mockJwtService->method('generateToken')->willReturn('accessToken');
        $this->mockJwtService->method('createOrUpdateRefreshToken')->willReturn($apiToken);

        $this->client->request('POST', self::LOGIN_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('access_token', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('refresh_token', $this->client->getResponse()->getContent());
    }

    public function testLoginInvalidCredentials()
    {
        $this->mockAccountService->method('loginUser')->willThrowException(new BadRequestException('Invalid email or password.'));

        $this->client->request('POST', self::LOGIN_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
            'password' => 'wrongpassword'
        ]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}
