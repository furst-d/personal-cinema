<?php

namespace App\Repository\Account;

use App\Entity\Account\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param string $keyword
     * @return Role|null
     */
    public function findByKeyword(string $keyword): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.keyName = :keyword')
            ->setParameter('keyword', $keyword)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
