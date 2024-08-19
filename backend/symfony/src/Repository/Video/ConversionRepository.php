<?php

namespace App\Repository\Video;

use App\Entity\Video\Conversion;
use App\Entity\Video\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversion>
 */
class ConversionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversion::class);
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
}
