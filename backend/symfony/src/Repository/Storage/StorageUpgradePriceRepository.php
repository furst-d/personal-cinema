<?php

namespace App\Repository\Storage;

use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgradePrice;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorageCardPayment>
 */
class StorageUpgradePriceRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageUpgradePrice::class);
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<StorageUpgradePrice>
     */
    public function getPrices(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('s');

        $this->applySort($paginatorRequest->getSort(), $paginatorRequest->getOrder(), $qb);

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @return StorageUpgradePrice[]
     */
    public function getAllBySize(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.size', 'ASC')
            ->getQuery()->getResult();
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
                $qb->orderBy('s.size', $order->value);
                break;
            case SortBy::PRICE_CZK:
                $qb->orderBy('s.priceCzk', $order->value);
                break;
            case SortBy::PERCENTAGE_DISCOUNT:
                $qb->orderBy('s.percentageDiscount', $order->value);
                break;
            case SortBy::DISCOUNT_EXPIRATION_AT:
                $qb->orderBy('s.discountExpirationAt', $order->value);
                break;
            default:
                $qb->orderBy('s.id', $order->value);
                break;
        }
    }

    /**
     * @param FilterRequest|null $filter
     * @param QueryBuilder $qb
     * @return void
     */
    public function applyFilter(?FilterRequest $filter, QueryBuilder $qb): void {}

    /**
     * @param int[] $ids
     * @return StorageUpgradePrice[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param StorageUpgradePrice $price
     * @return void
     */
    public function save(StorageUpgradePrice $price): void
    {
        $em = $this->getEntityManager();
        $em->persist($price);
        $em->flush();
    }

    /**
     * @param StorageUpgradePrice $price
     * @return void
     */
    public function delete(StorageUpgradePrice $price): void
    {
        $em = $this->getEntityManager();
        $em->remove($price);
        $em->flush();
    }
}
