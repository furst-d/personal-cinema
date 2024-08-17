<?php

namespace App\Service\Account;

use App\Entity\Account\Account;
use App\Exception\NotFoundException;
use App\Repository\Account\AccountRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenUserProvider implements UserProviderInterface
{
    /**
     * @var AccountRepository $accountRepository
     */
    private AccountRepository $accountRepository;

    /**
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return $class === Account::class;
    }

    /**
     * @param string $identifier
     * @return UserInterface
     * @throws NotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->accountRepository->find($identifier);

        if (!$user) {
            throw new NotFoundException('User not found.');
        }

        return $user;
    }
}
