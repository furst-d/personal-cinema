<?php

namespace App\Service\Account;

use App\Entity\Account\Account;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Authenticator\Authenticator;
use App\Repository\Account\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
     * @var Authenticator $authenticator
     */
    private Authenticator $authenticator;

    private const ACCOUNT_NOT_FOUND_MESSAGE = 'Account not found.';

    /**
     * @param EntityManagerInterface $em
     * @param RoleService $roleService
     * @param AccountRepository $accountRepository
     * @param Authenticator $authenticator
     */
    public function __construct(
        EntityManagerInterface $em,
        RoleService            $roleService,
        AccountRepository      $accountRepository,
        Authenticator          $authenticator
    )
    {
        $this->em = $em;
        $this->roleService = $roleService;
        $this->accountRepository = $accountRepository;
        $this->authenticator = $authenticator;
    }

    /**
     * @param string $email
     * @param string $password
     * @return Account
     * @throws InternalException
     */
    public function registerUser(string $email, string $password): Account
    {
        try {
            if ($this->accountRepository->findOneBy(['email' => $email])) {
                throw new ConflictException('Account already exists.');
            }

            $salt = $this->authenticator->generateSalt();
            $password = $this->authenticator->combinePassword($password, $salt);

            $user = new Account($email, $password, $salt);
            $this->roleService->addDefaultRole($user);

            $this->accountRepository->save($user);
        } catch (Exception) {
            throw new InternalException("Failed to register user.");
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

        if (!$user || !$this->authenticator->verifyPassword($password, $user->getPassword(), $user->getSalt())) {
            throw new BadRequestException('Invalid email or password.');
        }

        return $user;
    }

    /**
     * @param Account $account
     * @param string $password
     * @return Account
     */
    public function changePassword(Account $account, string $password): Account
    {
        $salt = $account->getSalt();
        $password = $this->authenticator->combinePassword($password, $salt);
        $account->setPassword($password);
        $this->em->flush();
        return $account;
    }

    /**
     * @param int $accountId
     * @return Account
     * @throws NotFoundException|BadRequestException
     */
    public function activateAccount(int $accountId): Account
    {
        /** @var Account $account */
        $account = $this->accountRepository->find($accountId);

        if (!$account) {
            throw new NotFoundException(self::ACCOUNT_NOT_FOUND_MESSAGE);
        }

        if ($account->isActive()) {
            throw new BadRequestException('Account is already activated.');
        }

        $account->setActive(true);
        $this->em->flush();

        return $account;
    }

    /**
     * @param string $email
     * @return Account
     * @throws NotFoundException
     */
    public function getAccountByEmail(string $email): Account
    {
        /** @var Account $user */
        $user = $this->accountRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundException(self::ACCOUNT_NOT_FOUND_MESSAGE);
        }

        return $user;
    }

    /**
     * @param int $id
     * @return Account
     * @throws NotFoundException
     */
    public function getAccountById(int $id): Account
    {
        /** @var Account $user */
        $user = $this->accountRepository->find($id);

        if (!$user) {
            throw new NotFoundException(self::ACCOUNT_NOT_FOUND_MESSAGE);
        }

        return $user;
    }
}
