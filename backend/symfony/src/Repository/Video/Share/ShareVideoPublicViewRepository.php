<?php

namespace App\Repository\Video\Share;

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
}
