<?php

namespace App\Repository\Video\Share;

use App\Entity\Video\Share\ShareVideoPublic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareVideoPublic>
 */
class ShareVideoPublicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareVideoPublic::class);
    }
}
