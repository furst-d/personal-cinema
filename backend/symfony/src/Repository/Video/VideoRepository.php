<?php

namespace App\Repository\Video;

use App\DTO\Filter\FilterRequest;
use App\DTO\Filter\VideoFilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Video;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Helper\Video\FolderData;
use App\Helper\Video\NameNormalizer;
use App\Repository\FilterSortInterface;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository implements FilterSortInterface
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
     * @param Video $video
     * @return void
     */
    public function save(Video $video): void
    {
        $em = $this->getEntityManager();

        $em->persist($video);
        $em->flush();
    }

    /**
     * @param Account|null $account
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filter
     * @return PaginatorResult<Video>
     */
    public function findVideos(?Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest, ?FilterRequest $filter): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->join('v.account', 'a')
            ->join('v.md5', 'm');

        if ($folderData->isDefaultFolder()) {
            $qb->andWhere('v.folder IS NULL');
            if ($account) {
                $qb->andWhere('(v.account = :account)')
                    ->setParameter('account', $account);
            }
        } else {
            if ($folderData->getFolder()) {
                $qb->andWhere('v.folder = :folder')->setParameter('folder', $folderData->getFolder());
            }
        }

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
        if (!$filter instanceof VideoFilterRequest) {
            return;
        }

        if (!empty($filter->email)) {
            $qb->andWhere('a.email LIKE :email')->setParameter('email', "%$filter->email%");
        }

        if (!empty($filter->name)) {
            $normalizedName = NameNormalizer::normalize($filter->name);
            $qb->andWhere('v.normalizedName LIKE :name')->setParameter('name', "%$normalizedName%");
        }

        if (!empty($filter->md5)) {
            $qb->andWhere('m.md5 LIKE :md5')->setParameter('md5', "%$filter->md5%");
        }

        if (!empty($filter->hash)) {
            $qb->andWhere('v.hash LIKE :hash')->setParameter('hash', "%$filter->hash%");
        }

        if (!empty($filter->cdnId)) {
            $qb->andWhere('v.cdnId LIKE :cdnId')->setParameter('cdnId', "%$filter->cdnId%");
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
            case SortBy::NAME:
                $qb->orderBy('v.name', $order->value);
                break;
            case SortBy::EMAIL:
                $qb->orderBy('a.email', $order->value);
                break;
            case SortBy::UPDATE_DATE:
                $qb->orderBy('v.createdAt', $order->value);
                break;
            case SortBy::LENGTH:
                $qb->orderBy('v.length', $order->value);
                break;
            case SortBy::SIZE:
                $qb->orderBy('v.size', $order->value);
                break;
            default:
                $qb->orderBy('v.id', $order->value);
                break;
        }
    }

    /**
     * @param Account $account
     * @param string $phrase
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function searchVideos(Account $account, string $phrase, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.account = :account')->setParameter('account', $account)
            ->andWhere('v.normalizedName LIKE :phrase')->setParameter('phrase', "%$phrase%")
            ->orderBy('v.createdAt', 'DESC');

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function findSharedVideos(Account $account, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->join('v.shares', 'sv')
            ->where('sv.account = :account')->setParameter('account', $account);

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Video $video
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function findRecommendations(Video $video, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->join('v.conversions', 'c')
            ->andWhere('v.id != :id')->setParameter('id', $video->getId())
            ->andWhere('v.account = :account')->setParameter('account', $video->getAccount())
            ->andWhere('v.thumbnail IS NOT NULL')
            ->orderBy('RAND()');

        if ($folder = $video->getFolder()) {
            $qb->andWhere('v.folder = :folder')->setParameter('folder', $folder);
        } else {
            $qb->andWhere('v.folder IS NULL');
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param array $ids
     * @return Video[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.id IN (:ids)')->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }

    /**
     * @param Video $video
     * @return void
     */
    public function delete(Video $video): void
    {
        $em = $this->getEntityManager();

        $em->remove($video);
        $em->flush();
    }
}
