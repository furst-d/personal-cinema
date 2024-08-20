<?php

namespace App\Repository\Account;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @param FilterRequest|null $filter
     * @return PaginatorResult<Account>
     */
    public function findAccounts(PaginatorRequest $paginatorRequest, ?FilterRequest $filter): PaginatorResult
    {
        $qb = $this->createQueryBuilder('a');

        if ($filter instanceof EmailFilterRequest) {
            $qb->andWhere('a.email LIKE :email')
                ->setParameter('email', "%$filter->email%");
        }

        $sort = $paginatorRequest->getSort();
        $order = $paginatorRequest->getOrder()->value;

        switch ($sort) {
            case SortBy::EMAIL:
                $qb->orderBy('a.email', $order);
                break;
            case SortBy::CREATE_DATE:
                $qb->orderBy('a.createdAt', $order);
                break;
            case SortBy::IS_ACTIVE:
                $qb->orderBy('a.isActive', $order);
                break;
            default:
                $qb->orderBy('a.id', $order);
                break;
        }

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

    /**
     * @param int[] $ids
     * @return Account[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id IN (:ids)')->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }
}
