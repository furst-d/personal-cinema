<?php

namespace App\Repository\Account;

use App\Entity\Account\Account;
use App\Helper\Paginator\PaginatorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
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

        if (!is_null($limit) && !is_null($offset)) {
            $qb->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        $paginator = new Paginator($qb);
        $totalItems = $paginator->count();

        return new PaginatorResult(iterator_to_array($paginator), $totalItems);
    }
}
