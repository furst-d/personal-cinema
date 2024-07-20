<?php

namespace App\Tests\Service\Account;

use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Exception\NotFoundException;
use App\Repository\Account\RoleRepository;
use App\Service\Account\RoleService;
use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private const ROLE_USER = 'ROLE_USER';
    private const ROLE_ADMIN = 'ROLE_ADMIN';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_PASSWORD = 'password';
    private const TEST_SALT = 'salt';

    private $roleRepository;
    private $roleService;
    private $account;

    protected function setUp(): void
    {
        $this->roleRepository = $this->createMock(RoleRepository::class);
        $this->roleService = new RoleService($this->roleRepository);
        $this->account = new Account(self::TEST_EMAIL, self::TEST_PASSWORD, self::TEST_SALT);
    }

    public function testAddDefaultRoleSuccess()
    {
        $role = new Role('User', self::ROLE_USER);
        $this->roleRepository->method('findByKeyword')->willReturn($role);

        $result = $this->roleService->addDefaultRole($this->account);

        $this->assertInstanceOf(Account::class, $result);

        $this->assertTrue($this->roleExistsInAccount($result, self::ROLE_USER));
    }

    public function testAddRoleByKeywordNotFound()
    {
        $this->roleRepository->method('findByKeyword')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->roleService->addDefaultRole($this->account);
    }

    public function testIsAdminWithAdminRole()
    {
        $role = new Role('Admin', self::ROLE_ADMIN);
        $this->account->addRole($role);

        $this->assertTrue($this->roleService->isAdmin($this->account));
    }

    public function testIsAdminWithoutAdminRole()
    {
        $role = new Role('User', self::ROLE_USER);
        $this->account->addRole($role);

        $this->assertFalse($this->roleService->isAdmin($this->account));
    }

    private function roleExistsInAccount(Account $account, string $roleName): bool
    {
        foreach ($account->getRoles() as $role) {
            if ($role['key'] === $roleName) {
                return true;
            }
        }
        return false;
    }
}
