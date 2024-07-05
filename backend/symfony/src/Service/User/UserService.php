<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Helper\Api\Exception\ConflictException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Api\Exception\NotFoundException;
use App\Helper\Authenticator\Authenticator;
use App\Repository\User\RoleRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

class UserService
{
    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    private RoleService $roleService;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    /**
     * @param EntityManagerInterface $em
     * @param RoleService $roleService
     * @param UserRepository $userRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        RoleService $roleService,
        UserRepository $userRepository,
    )
    {
        $this->em = $em;
        $this->roleService = $roleService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws ConflictException|NotFoundException|InternalException
     */
    public function registerUser(string $email, string $password): User
    {
        try {
            if ($this->userRepository->findOneBy(['email' => $email])) {
                throw new ConflictException('User already exists.');
            }

            $salt = Authenticator::generateSalt();
            $password = Authenticator::combinePassword($password, $salt);

            $user = new User($email, $password, $salt);
            $this->roleService->addDefaultRole($user);

            $this->em->persist($user);
            $this->em->flush();

            //TODO send activation email
        } catch (RandomException $e) {
            throw new InternalException($e->getMessage());
        }

        return $user;
    }
}
