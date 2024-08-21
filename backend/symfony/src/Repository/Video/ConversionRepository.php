<?php

namespace App\Repository\Video;

use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Video\Conversion;
use App\Entity\Video\Video;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversion>
 */
class ConversionRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversion::class);
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Conversion>
     */
    public function findConversions(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('c');
        $this->applySort($paginatorRequest->getSort(), $paginatorRequest->getOrder(), $qb);

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param FilterRequest|null $filter
     * @param QueryBuilder $qb
     * @return void
     */
    public function applyFilter(?FilterRequest $filter, QueryBuilder $qb): void {}

    /**
     * @param SortBy $sort
     * @param OrderBy $order
     * @param QueryBuilder $qb
     * @return void
     */
    public function applySort(SortBy $sort, OrderBy $order, QueryBuilder $qb): void
    {
        switch ($sort) {
            case SortBy::WIDTH:
                $qb->orderBy('c.width', $order->value);
                break;
            case SortBy::HEIGHT:
                $qb->orderBy('c.height', $order->value);
                break;
            case SortBy::BANDWIDTH:
                $qb->orderBy('c.bandwidth', $order->value);
                break;
            default:
                $qb->orderBy('c.id', $order->value);
                break;
        }
    }

    /**
     * @param Video $video
     * @param array $heights
     * @return Conversion[]
     */
    public function findUnusedConversions(Video $video, array $heights): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.videos', 'v', 'WITH', 'v.id = :video')
            ->where('c.height IN (:heights)')
            ->andWhere('v.id IS NULL')
            ->setParameter('heights', $heights)
            ->setParameter('video', $video)
            ->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @return Conversion[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * @param Conversion $conversion
     * @return void
     */
    public function save(Conversion $conversion): void
    {
        $em = $this->getEntityManager();
        $em->persist($conversion);
        $em->flush();
    }

    /**
     * @param Conversion $conversion
     * @return void
     */
    public function delete(Conversion $conversion): void
    {
        $em = $this->getEntityManager();
        $em->remove($conversion);
        $em->flush();
    }
}
