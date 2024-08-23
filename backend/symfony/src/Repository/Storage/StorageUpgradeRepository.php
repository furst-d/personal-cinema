<?php

namespace App\Repository\Storage;

use App\DTO\Filter\FilterRequest;
use App\DTO\Filter\StorageUpgradeFilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Storage\StorageUpgrade;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorageUpgrade>
 */
class StorageUpgradeRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageUpgrade::class);
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filterRequest
     * @return PaginatorResult<StorageUpgrade>
     */
    public function getUpgrades(PaginatorRequest $paginatorRequest, ?FilterRequest $filterRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.account', 'a')
            ->leftJoin('u.storageCardPayment', 'p');

        $this->applySort($paginatorRequest->getSort(), $paginatorRequest->getOrder(), $qb);
        $this->applyFilter($filterRequest, $qb);

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param int[] $ids
     * @return StorageUpgrade[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param FilterRequest|null $filter
     * @param QueryBuilder $qb
     * @return void
     */
    public function applyFilter(?FilterRequest $filter, QueryBuilder $qb): void
    {
        if (!$filter instanceof StorageUpgradeFilterRequest) {
            return;
        }

        if (!empty($filter->email)) {
            $qb->andWhere('a.email LIKE :email')
                ->setParameter('email', "%$filter->email%");
        }

        if (!empty($filter->stripeSessionId)) {
            $qb->andWhere('p.sessionId LIKE :sessionId')
                ->setParameter('sessionId', "%$filter->stripeSessionId%");
        }

        if (!empty($filter->paymentTypeId)) {
            $qb->andWhere('u.paymentType = :paymentTypeId')
                ->setParameter('paymentTypeId', $filter->paymentTypeId);
        }
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
            case SortBy::SIZE:
                $qb->orderBy('u.size', $order->value);
                break;
            case SortBy::PRICE_CZK:
                $qb->orderBy('u.priceCzk', $order->value);
                break;
            case SortBy::CREATE_DATE:
                $qb->orderBy('u.createdAt', $order->value);
                break;
            case SortBy::EMAIL:
                $qb->orderBy('a.email', $order->value);
                break;
            default:
                $qb->orderBy('u.id', $order->value);
                break;
        }
    }

    /**
     * @param StorageUpgrade $storageUpgrade
     * @return void
     */
    public function save(StorageUpgrade $storageUpgrade): void
    {
        $em = $this->getEntityManager();
        $em->persist($storageUpgrade);
        $em->flush();
    }

    /**
     * @param StorageUpgrade $upgrade
     * @return void
     */
    public function delete(StorageUpgrade $upgrade): void
    {
        $em = $this->getEntityManager();
        $em->remove($upgrade);
        $em->flush();
    }
}
