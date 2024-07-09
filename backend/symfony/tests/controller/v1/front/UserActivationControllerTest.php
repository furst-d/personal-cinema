<?php

namespace App\Tests\Controller\v1;

use App\Entity\Account\Account;
use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserActivationControllerTest extends WebTestCase
{
    private const TEST_EMAIL = 'test@example.com';
    private const ACTIVATE_URL = '/v1/users/activate';
    private const ACTIVATE_SEND_URL = '/v1/users/activate/send';
    private const APPLICATION_JSON = ['CONTENT_TYPE' => 'application/json'];

    private $client;
    private $mockAccountService;
    private $mockJwtService;
    private $mockMailerService;
    private $account;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->mockAccountService = $this->createMock(AccountService::class);
        $this->mockJwtService = $this->createMock(JwtService::class);
        $this->mockMailerService = $this->createMock(MailerService::class);

        $this->client->getContainer()->set(AccountService::class, $this->mockAccountService);
        $this->client->getContainer()->set(JwtService::class, $this->mockJwtService);
        $this->client->getContainer()->set(MailerService::class, $this->mockMailerService);

        $this->account = new Account(self::TEST_EMAIL, 'password', 'salt');
    }

    public function testActivateSuccess()
    {
        $this->mockJwtService->method('decodeToken')->willReturn(['user_id' => 1]);
        $this->mockAccountService->method('activateAccount')->willReturn($this->account);

        $this->client->request('POST', self::ACTIVATE_URL, [], [], self::APPLICATION_JSON, json_encode([
            'token' => 'validToken'
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('User was activated successfully.', $this->client->getResponse()->getContent());
    }

    public function testActivateInvalidToken()
    {
        $this->mockJwtService->method('decodeToken')->willThrowException(new UnauthorizedException('Invalid token.'));

        $this->client->request('POST', self::ACTIVATE_URL, [], [], self::APPLICATION_JSON, json_encode([
            'token' => 'invalidToken'
        ]));

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Invalid token.', $this->client->getResponse()->getContent());
    }

    public function testActivateUserNotFound()
    {
        $this->mockJwtService->method('decodeToken')->willReturn(['user_id' => 999]);
        $this->mockAccountService->method('activateAccount')->willThrowException(new NotFoundException('Account not found.'));

        $this->client->request('POST', self::ACTIVATE_URL, [], [], self::APPLICATION_JSON, json_encode([
            'token' => 'validToken'
        ]));

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Account not found.', $this->client->getResponse()->getContent());
    }

    public function testSendActivationEmailSuccess()
    {
        $this->mockAccountService->method('getAccountByEmail')->willReturn($this->account);
        $this->mockJwtService->method('generateToken')->willReturn('activationToken');
        $this->mockMailerService->expects($this->once())->method('sendAccountActivation');

        $this->client->request('POST', self::ACTIVATE_SEND_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Activation email was sent successfully.', $this->client->getResponse()->getContent());
    }

    public function testSendActivationEmailUserNotFound()
    {
        $this->mockAccountService->method('getAccountByEmail')->willThrowException(new NotFoundException('User not found.'));

        $this->client->request('POST', self::ACTIVATE_SEND_URL, [], [], self::APPLICATION_JSON, json_encode([
            'email' => self::TEST_EMAIL,
        ]));

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('User not found.', $this->client->getResponse()->getContent());
    }
}
