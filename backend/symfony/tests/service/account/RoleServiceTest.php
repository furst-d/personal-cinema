<?php

namespace App\Tests\Service\Account;

use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Helper\Api\Exception\NotFoundException;
use App\Repository\Account\RoleRepository;
use App\Service\Account\RoleService;
use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private const ROLE_USER = 'ROLE_USER';
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
        $this->assertContains($role->getKeyName(), $result->getRoles());
    }

    public function testAddRoleByKeywordNotFound()
    {
        $this->roleRepository->method('findByKeyword')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->roleService->addDefaultRole($this->account);
    }
}
