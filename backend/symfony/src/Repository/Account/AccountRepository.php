<?php

namespace App\Repository\Account;

use App\DTO\PaginatorRequest;
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
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Account>
     */
    public function findAccounts(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('a');

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @return void
     */
    public function delete(Account $account): void
    {
        $em = $this->getEntityManager();
        $em->remove($account);
        $em->flush();
    }
}
