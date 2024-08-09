<?php

namespace App\Repository\Video\Share;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Video;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareVideo>
 */
class ShareVideoRepository extends ServiceEntityRepository
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareVideo::class);
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return bool
     */
    public function hasSharedVideoAccess(Account $account, Video $video): bool
    {
        return $this->createQueryBuilder('sv')
            ->select('COUNT(sv)')
            ->where('sv.video = :video')->setParameter('video', $video)
            ->andWhere('sv.account = :account')->setParameter('account', $account)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Account $account
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function findSharedVideos(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('sv')
            ->where('sv.account = :account')->setParameter('account', $account);

        if ($folderData->isDefaultFolder()) {
            $qb->andWhere('sv.video.folder.parent IS NULL');
        } else {
            if ($folderData->getFolder()) {
                $qb->andWhere('sv.video.folder.parent = :folder')->setParameter('folder', $folderData->getFolder());
            }
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return bool
     */
    public function isVideoAlreadyShared(Account $account, Video $video): bool
    {
        $qb = $this->createQueryBuilder('sv')
            ->select('COUNT(sv)')
            ->where('sv.account = :account')->setParameter('account', $account)
            ->andWhere('sv.video = :video')->setParameter('video', $video);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param ShareVideo $shareVideo
     * @return void
     */
    public function save(ShareVideo $shareVideo): void
    {
        $em = $this->getEntityManager();
        $em->persist($shareVideo);
        $em->flush();
    }
}
