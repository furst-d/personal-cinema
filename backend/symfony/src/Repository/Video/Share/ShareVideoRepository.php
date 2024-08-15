<?php

namespace App\Repository\Video\Share;

use App\Entity\Account\Account;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareVideo>
 */
class ShareVideoRepository extends ServiceEntityRepository
{
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

    /**
     * @param ShareVideo $videoShare
     * @return void
     */
    public function delete(ShareVideo $videoShare): void
    {
        $em = $this->getEntityManager();
        $em->remove($videoShare);
        $em->flush();
    }
}
