<?php

namespace App\Service\Account;

use App\Entity\User\Account;
use App\Helper\Api\Exception\NotFoundException;
use App\Repository\Account\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

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
}
