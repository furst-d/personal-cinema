<?php

namespace App\Repository\Video;

use App\ORM\RandomFunction;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Video;
use App\Helper\DTO\PaginatorResult;
use App\Helper\DTO\SortBy;
use App\Helper\Video\FolderData;
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
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function findVideos(?Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v');

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

        if ($sortBy = $paginatorRequest->getOrderBy()) {
            if ($sortBy === SortBy::NAME) {
                $qb->orderBy('v.name');
            } elseif ($sortBy === SortBy::UPDATE_DATE) {
                $qb->orderBy('v.createdAt', 'DESC');
            }
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
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
