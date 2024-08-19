<?php

namespace App\Service\Account;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Authenticator\Authenticator;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Storage\ByteSizeConverter;
use App\Helper\Video\ThirdParty;
use App\Repository\Account\AccountRepository;
use App\Service\Storage\StorageService;
use App\Service\Video\VideoService;
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
     * @var StorageService $storageService
     */
    private StorageService $storageService;

    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

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
     * @param StorageService $storageService
     * @param VideoService $videoService
     * @param AccountRepository $accountRepository
     * @param Authenticator $authenticator
     */
    public function __construct(
        EntityManagerInterface $em,
        RoleService $roleService,
        StorageService $storageService,
        VideoService $videoService,
        AccountRepository $accountRepository,
        Authenticator $authenticator,
    )
    {
        $this->em = $em;
        $this->roleService = $roleService;
        $this->storageService = $storageService;
        $this->videoService = $videoService;
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
            $storageSize = $this->storageService->getDefaultUserStorageLimit();

            $user = new Account($email, $password, $salt, $storageSize);
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
        $user = $this->accountRepository->findOneBy(['email' => $email]);

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
     * @param string $oldPassword
     * @param string $newPassword
     * @return Account
     * @throws BadRequestException
     */
    public function checkAndChangePassword(Account $account, string $oldPassword, string $newPassword): Account
    {
        if (!$this->authenticator->verifyPassword($oldPassword, $account->getPassword(), $account->getSalt())) {
            throw new BadRequestException('Current password does not match.');
        }

        $this->changePassword($account, $newPassword);

        return $account;
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
        $user = $this->accountRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundException(self::ACCOUNT_NOT_FOUND_MESSAGE);
        }

        return $user;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Account>
     */
    public function getAccounts(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->accountRepository->findAccounts($paginatorRequest);
    }

    /**
     * @param Account $account
     * @return array
     */
    public function getStats(Account $account): array
    {
        return [
            'email' => $account->getEmail(),
            'storageUsedGB' => ByteSizeConverter::toGB($account->getStorage()->getUsedStorage()),
            'storageLimitGB' => ByteSizeConverter::toGB($account->getStorage()->getMaxStorage()),
            'storageUpgradeCount' => count($account->getStorageUpgrades()),
            'videosCount' => count($account->getVideos()),
            'foldersCount' => count($account->getFolders()),
            'sharedVideosCount' => count($account->getSharedVideos()),
            'sharedFoldersCount' => count($account->getSharedFolders()),
            'created' => $account->getCreatedAt(),
        ];
    }

    /**
     * @param Account $account
     * @param string $password
     * @return void
     * @throws ForbiddenException
     */
    public function checkPasswordAndDeleteAccount(Account $account, string $password): void
    {
        if (!$this->authenticator->verifyPassword($password, $account->getPassword(), $account->getSalt())) {
            throw new ForbiddenException('Invalid password.');
        }

        $this->deleteAccount($account);
    }

    public function deleteAccount(Account $account): void
    {
        $this->videoService->deleteVideos($account->getVideos()->toArray(), [ThirdParty::CDN]);

        $this->accountRepository->delete($account);
    }
}
