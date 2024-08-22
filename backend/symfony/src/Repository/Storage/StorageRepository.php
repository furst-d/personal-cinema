<?php

namespace App\Repository\Storage;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Storage\Storage;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Storage>
 */
class StorageRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Storage::class);
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filterRequest
     * @return PaginatorResult<Storage>
     */
    public function getStorages(PaginatorRequest $paginatorRequest, ?FilterRequest $filterRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.account', 'a');

        $this->applyFilter($filterRequest, $qb);
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
            case SortBy::MAX_STORAGE:
                $qb->orderBy('s.maxStorage', $order->value);
                break;
            case SortBy::USED_STORAGE:
                $qb->orderBy('s.usedStorage', $order->value);
                break;
            case SortBy::FILL_SIZE:
                $qb->orderBy('(s.usedStorage * 1.0) / s.maxStorage', $order->value);
                break;
            default:
                $qb->orderBy('s.id', $order->value);
                break;
        }
    }

    /**
     * @param Storage $storage
     * @return void
     */
    public function save(Storage $storage): void
    {
        $em = $this->getEntityManager();
        $em->persist($storage);
        $em->flush();
    }
}
