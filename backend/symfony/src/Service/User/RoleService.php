<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Helper\Api\Exception\NotFoundException;
use App\Repository\User\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var RoleRepository $roleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * @param EntityManagerInterface $em
     * @param RoleRepository $roleRepository
     */
    public function __construct(EntityManagerInterface $em, RoleRepository $roleRepository)
    {
        $this->em = $em;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param User $user
     * @return User
     * @throws NotFoundException
     */
    public function addDefaultRole(User $user): User
    {
        return $this->addRoleByKeyword($user, 'ROLE_USER');
    }

    /**
     * @param User $user
     * @param string $keyword
     * @return User
     * @throws NotFoundException
     */
    private function addRoleByKeyword(User $user, string $keyword): User
    {
        $role = $this->roleRepository->findByKeyword($keyword);
        if (!$role) {
            throw new NotFoundException("Role $keyword not found.");
        }

        $user->addRole($role);
        return $user;
    }
}
