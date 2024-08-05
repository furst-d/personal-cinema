<?php

namespace App\Repository\Video;

use App\DQL\RandomFunction;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Video;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
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
     * @param Folder|null $folder
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function findVideos(?Account $account, ?Folder $folder, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.isDeleted = false');

        if ($account) {
            $qb->andWhere('v.account = :account')->setParameter('account', $account);
        }

        if ($folder) {
            $qb->andWhere('v.folder = :folder')->setParameter('folder', $folder);
        }

        if ($sortBy = $paginatorRequest->getOrderBy()) {
            if ($sortBy === SortBy::NAME) {
                $qb->orderBy('v.name');
            }
        }

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
            ->where('v.isDeleted = false')
            ->andWhere('v.id != :id')->setParameter('id', $video->getId())
            ->andWhere('v.account = :account')->setParameter('account', $video->getAccount())
            ->andWhere('v.path IS NOT NULL')
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
