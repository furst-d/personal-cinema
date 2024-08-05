<?php

namespace App\Repository\Account;

use App\Entity\Account\Account;
use App\Helper\DTO\PaginatorResult;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    use PaginatorHelper;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param Account $user
     * @return void
     */
    public function save(Account $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Account>
     */
    public function findAccounts(?int $limit, ?int $offset): PaginatorResult
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isDeleted = false');

        return $this->getPaginatorResult($qb, $limit, $offset);
    }
}
