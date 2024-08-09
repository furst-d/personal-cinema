<?php

namespace App\Repository\Video\Share;

use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Share\ShareVideoPublicView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareVideoPublicView>
 */
class ShareVideoPublicViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareVideoPublicView::class);
    }

    /**
     * @param ShareVideoPublic $publicVideoShare
     * @param string $sessionId
     * @return ShareVideoPublicView[]
     */
    public function findShareViews(ShareVideoPublic $publicVideoShare, string $sessionId): array
    {
        return $this->createQueryBuilder('svp')
            ->where('svp.shareVideoPublic = :publicVideoShare')->setParameter('publicVideoShare', $publicVideoShare)
            ->andWhere('svp.sessionId = :sessionId')->setParameter('sessionId', $sessionId)
            ->getQuery()->getResult();
    }

    /**
     * @param ShareVideoPublicView $view
     * @return void
     */
    public function save(ShareVideoPublicView $view): void
    {
        $em = $this->getEntityManager();
        $em->persist($view);
        $em->flush();
    }
}
