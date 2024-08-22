<?php

namespace App\Repository\Settings;

use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Settings\Settings;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Settings>
 */
class SettingsRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    /**
     * @return string
     */
    public function getMaxFileSize(): string
    {
        return $this->findOneBy(['key' => 'video_size_limit'])->getValue();
    }

    /**
     * @return string
     */
    public function getDefaultUserStorageLimit(): string
    {
        return $this->findOneBy(['key' => 'default_user_storage_limit'])->getValue();
    }

    /**
     * @return string
     */
    public function getPublicLinkViewLimit(): string
    {
        return $this->findOneBy(['key' => 'public_link_limit'])->getValue();
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Settings>
     */
    public function getSettings(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('s');

        $this->applySort($paginatorRequest->getSort(), $paginatorRequest->getOrder(), $qb);

        return $this->getPaginatorResult($qb, $paginatorRequest);
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
            case SortBy::KEY:
                $qb->orderBy('s.key', $order->value);
                break;
            case SortBy::VALUE:
                $qb->orderBy('s.value', $order->value);
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
     * @return Settings[]
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
     * @param Settings $setting
     * @return void
     */
    public function save(Settings $setting): void
    {
        $em = $this->getEntityManager();
        $em->persist($setting);
        $em->flush();
    }

    /**
     * @param Settings $setting
     * @return void
     */
    public function delete(Settings $setting): void
    {
        $em = $this->getEntityManager();
        $em->remove($setting);
        $em->flush();
    }
}
