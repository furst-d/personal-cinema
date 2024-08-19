<?php

namespace App\Tests\Service\Account;

use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Authenticator\Authenticator;
use App\Repository\Account\AccountRepository;
use App\Service\Account\AccountService;
use App\Service\Account\RoleService;
use App\Service\Storage\StorageService;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_PASSWORD = 'password';
    private const TEST_SALT = 'salt';
    private const TEST_ACCOUNT_ID = 1;

    private $entityManager;
    private $roleService;
    private $storageService;
    private $videoService;
    private $accountRepository;
    private $authenticator;
    private $accountService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->roleService = $this->createMock(RoleService::class);
        $this->storageService = $this->createMock(StorageService::class);
        $this->videoService = $this->createMock(VideoService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->authenticator = $this->createMock(Authenticator::class);

        $this->accountService = new AccountService(
            $this->entityManager,
            $this->roleService,
            $this->storageService,
            $this->videoService,
            $this->accountRepository,
            $this->authenticator
        );
    }

    public function testRegisterUserSuccess()
    {
        $this->accountRepository->method('findOneBy')->willReturn(null);
        $this->roleService->method('addDefaultRole')->willReturn(new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10));

        $salt = 'random_salt';
        $hashedPassword = hash('sha256', self::TEST_PASSWORD . $salt);

        $this->authenticator->method('generateSalt')->willReturn($salt);
        $this->authenticator->method('combinePassword')->willReturn($hashedPassword);

        $account = $this->accountService->registerUser(self::TEST_EMAIL, self::TEST_PASSWORD);

        $this->assertInstanceOf(Account::class, $account);
        $this->assertEquals(self::TEST_EMAIL, $account->getEmail());
    }

    public function testRegisterUserInternalException()
    {
        $this->accountRepository->method('findOneBy')->willReturn(null);
        $this->roleService->method('addDefaultRole')->willReturn(new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10));

        $this->authenticator->method('generateSalt')->willThrowException(new Exception());

        $this->expectException(InternalException::class);

        $this->accountService->registerUser(self::TEST_EMAIL, self::TEST_PASSWORD);
    }

    public function testLoginUserSuccess()
    {
        $account = new Account(self::TEST_EMAIL, hash('sha256', self::TEST_PASSWORD . self::TEST_SALT), self::TEST_SALT, 10);
        $account->setActive(true);
        $account->addRole(new Role('User', 'ROLE_USER'));

        $this->accountRepository->method('findOneBy')->willReturn($account);
        $this->authenticator->method('verifyPassword')->willReturn(true);
        $this->roleService->method('hasRoles')->willReturn(true);

        $result = $this->accountService->loginUser(self::TEST_EMAIL, self::TEST_PASSWORD, ['ROLE_USER']);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals(self::TEST_EMAIL, $result->getEmail());
    }


    public function testChangePasswordSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10);
        $newPassword = 'new_password';
        $hashedPassword = hash('sha256', $newPassword . self::TEST_SALT);

        $this->authenticator->method('combinePassword')->willReturn($hashedPassword);

        $updatedAccount = $this->accountService->changePassword($account, $newPassword);

        $this->assertEquals($hashedPassword, $updatedAccount->getPassword());
    }

    public function testActivateAccountSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10);
        $account->setId(self::TEST_ACCOUNT_ID);
        $this->accountRepository->method('find')->willReturn($account);

        $activatedAccount = $this->accountService->activateAccount(self::TEST_ACCOUNT_ID);

        $this->assertTrue($activatedAccount->isActive());
    }

    public function testActivateAccountNotFound()
    {
        $this->accountRepository->method('find')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->accountService->activateAccount(self::TEST_ACCOUNT_ID);
    }

    public function testActivateAccountAlreadyActive()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10);
        $account->setActive(true);
        $this->accountRepository->method('find')->willReturn($account);

        $this->expectException(BadRequestException::class);

        $this->accountService->activateAccount(self::TEST_ACCOUNT_ID);
    }

    public function testGetAccountByEmailSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10);
        $this->accountRepository->method('findOneBy')->willReturn($account);

        $result = $this->accountService->getAccountByEmail(self::TEST_EMAIL);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals(self::TEST_EMAIL, $result->getEmail());
    }

    public function testGetAccountByEmailNotFound()
    {
        $this->accountRepository->method('findOneBy')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->accountService->getAccountByEmail(self::TEST_EMAIL);
    }

    public function testGetAccountByIdSuccess()
    {
        $account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT, 10);
        $this->accountRepository->method('findOneBy')->willReturn($account);

        $result = $this->accountService->getAccountById(self::TEST_ACCOUNT_ID);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals(self::TEST_EMAIL, $result->getEmail());
    }

    public function testGetAccountByIdNotFound()
    {
        $this->accountRepository->method('find')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->accountService->getAccountById(self::TEST_ACCOUNT_ID);
    }
}
