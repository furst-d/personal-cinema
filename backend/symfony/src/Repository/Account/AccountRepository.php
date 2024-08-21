<?php

namespace App\Repository\Account;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository implements FilterSortInterface
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

        $this->applyFilter($filter, $qb);
        $this->applySort($paginatorRequest->getSort(), $paginatorRequest->getOrder(), $qb);

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param FilterRequest|null $filter
     * @param QueryBuilder $qb
     * @return void
     */
    public function applyFilter(?FilterRequest $filter, QueryBuilder $qb): void
    {
        if (!$filter instanceof EmailFilterRequest) {
            return;
        }

        $qb->andWhere('a.email LIKE :email')
            ->setParameter('email', "%$filter->email%");
    }

    /**
     * @param SortBy $sort
     * @param OrderBy $order
     * @param QueryBuilder $qb
     * @return void
     */
    public function applySort(SortBy $sort, OrderBy $order, QueryBuilder $qb): void
    {
        switch ($sort) {
            case SortBy::EMAIL:
                $qb->orderBy('a.email', $order->value);
                break;
            case SortBy::CREATE_DATE:
                $qb->orderBy('a.createdAt', $order->value);
                break;
            case SortBy::IS_ACTIVE:
                $qb->orderBy('a.isActive', $order->value);
                break;
            default:
                $qb->orderBy('a.id', $order->value);
                break;
        }
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
