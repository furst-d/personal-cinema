<?php

namespace App\Service\Account;

use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Exception\NotFoundException;
use App\Repository\Account\RoleRepository;

class RoleService
{
    /**
     * @var RoleRepository $roleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Account $user
     * @return Account
     * @throws NotFoundException
     */
    public function addDefaultRole(Account $user): Account
    {
        return $this->addRoleByKeyword($user, 'ROLE_USER');
    }

    /**
     * @param Account $account
     * @return bool
     */
    public function isAdmin(Account $account): bool
    {
        return $this->hasRole($account, 'ROLE_ADMIN');
    }

    /**
     * @param Account $user
     * @param array $roles
     * @return bool
     */
    public function hasRoles(Account $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($user, $role)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Account $account
     * @param string $keyword
     * @return bool
     */
    private function hasRole(Account $account, string $keyword): bool
    {
        foreach ($account->getRoles() as $role) {
            if ($role['key'] === $keyword) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Account $user
     * @param string $keyword
     * @return Account
     * @throws NotFoundException
     */
    private function addRoleByKeyword(Account $user, string $keyword): Account
    {
        $role = $this->roleRepository->findByKeyword($keyword);
        if (!$role) {
            throw new NotFoundException("Role $keyword not found.");
        }

        $user->addRole($role);
        return $user;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roleRepository->findAll();
    }

    /**
     * @param array $roles
     * @return Role[]
     */
    public function getRolesByKeywords(array $roles): array
    {
        return $this->roleRepository->findByKeywords($roles);
    }
}
