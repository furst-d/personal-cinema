<?php

namespace App\Service\Account;

use App\Entity\User\Account;
use App\Helper\Api\Exception\BadRequestException;
use App\Helper\Api\Exception\ConflictException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Api\Exception\NotFoundException;
use App\Helper\Authenticator\Authenticator;
use App\Repository\Account\AccountRepository;
use App\Service\Jwt\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

class AccountService
{
    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var RoleService $roleService
     */
    private RoleService $roleService;

    /**
     * @var AccountRepository $accountRepository
     */
    private AccountRepository $accountRepository;

    /**
     * @param EntityManagerInterface $em
     * @param RoleService $roleService
     * @param AccountRepository $accountRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        RoleService            $roleService,
        AccountRepository      $accountRepository,
    )
    {
        $this->em = $em;
        $this->roleService = $roleService;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param string $email
     * @param string $password
     * @return Account
     * @throws ConflictException|NotFoundException|InternalException
     */
    public function registerUser(string $email, string $password): Account
    {
        try {
            if ($this->accountRepository->findOneBy(['email' => $email])) {
                throw new ConflictException('User already exists.');
            }

            $salt = Authenticator::generateSalt();
            $password = Authenticator::combinePassword($password, $salt);

            $user = new Account($email, $password, $salt);
            $this->roleService->addDefaultRole($user);

            $this->em->persist($user);
            $this->em->flush();

        } catch (RandomException $e) {
            throw new InternalException($e->getMessage());
        }

        return $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @return Account
     * @throws BadRequestException
     */
    public function loginUser(string $email, string $password): Account
    {
        $user = $this->accountRepository->findOneBy(['email' => $email, 'isDeleted' => false]);

        if (!$user || !Authenticator::verifyPassword($password, $user->getPassword(), $user->getSalt())) {
            throw new BadRequestException('Invalid email or password.');
        }

        return $user;
    }
}
