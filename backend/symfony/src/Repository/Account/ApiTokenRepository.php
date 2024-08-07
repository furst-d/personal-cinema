<?php

namespace App\Repository\Account;

use App\Entity\Account\ApiToken;
use App\Entity\Account\Account;
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
     * @param ApiToken $refreshToken
     * @return void
     */
    public function save(ApiToken $refreshToken): void
    {
        $em = $this->getEntityManager();
        $em->persist($refreshToken);
        $em->flush();
    }
}
