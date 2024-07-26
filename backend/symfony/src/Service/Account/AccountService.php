<?php

namespace App\Service\Account;

use App\Entity\Account\Account;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Authenticator\Authenticator;
use App\Helper\Paginator\PaginatorResult;
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
     * @throws InternalException|ConflictException
     */
    public function registerUser(string $email, string $password): Account
    {
        if ($this->accountRepository->findOneBy(['email' => $email])) {
            throw new ConflictException('Account already exists.');
        }

        try {
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
     * @param array $roles
     * @return Account
     * @throws BadRequestException|UnauthorizedException
     */
    public function loginUser(string $email, string $password, array $roles): Account
    {
        $user = $this->accountRepository->findOneBy(['email' => $email, 'isDeleted' => false]);

        if (!$user || !$this->authenticator->verifyPassword($password, $user->getPassword(), $user->getSalt())) {
            throw new BadRequestException('Invalid email or password.');
        }

        if (!$this->roleService->hasRoles($user, $roles)) {
            throw new UnauthorizedException('User does not have sufficient permissions.');
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
        $user = $this->accountRepository->findOneBy(['email' => $email, 'isDeleted' => false]);

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
        $user = $this->accountRepository->findOneBy(['id' => $id, 'isDeleted' => false]);

        if (!$user) {
            throw new NotFoundException(self::ACCOUNT_NOT_FOUND_MESSAGE);
        }

        return $user;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Account>
     */
    public function getAccounts(?int $limit, ?int $offset): PaginatorResult
    {
        return $this->accountRepository->findAccounts($limit, $offset);
    }
}
