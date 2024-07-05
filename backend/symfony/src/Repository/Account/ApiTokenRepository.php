<?php

namespace App\Repository\Account;

use App\Entity\User\ApiToken;
use App\Entity\User\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiToken>
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    /**
     * @param Account $account
     * @param string $sessionId
     * @return ApiToken|null
     */
    public function findByUserAndSession(Account $account, string $sessionId): ?ApiToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.account = :account')
            ->andWhere('t.sessionId = :sessionId')
            ->setParameter('account', $account)
            ->setParameter('sessionId', $sessionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $token
     * @return ApiToken|null
     */
    public function findToken(string $token): ?ApiToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.refreshToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
