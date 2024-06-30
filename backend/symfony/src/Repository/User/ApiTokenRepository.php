<?php

namespace App\Repository\User;

use App\Entity\User\ApiToken;
use App\Entity\User\User;
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
     * @param User $user
     * @param string $sessionId
     * @return ApiToken|null
     */
    public function findByUserAndSession(User $user, string $sessionId): ?ApiToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->andWhere('t.sessionId = :sessionId')
            ->setParameter('user', $user)
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
