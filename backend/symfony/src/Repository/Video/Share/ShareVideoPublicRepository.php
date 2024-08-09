<?php

namespace App\Repository\Video\Share;

use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Video;
use DateTimeImmutable;
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

    /**
     * @param string $hash
     * @return ShareVideoPublic|null
     */
    public function findValidByHash(string $hash): ?ShareVideoPublic
    {
        return $this->createQueryBuilder('svp')
            ->where('svp.hash = :hash')->setParameter('hash', $hash)
            ->andWhere('svp.expiredAt > :now')->setParameter('now', new DateTimeImmutable())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Video $video
     * @return ShareVideoPublic|null
     */
    public function findValidByVideo(Video $video): ?ShareVideoPublic
    {
        return $this->createQueryBuilder('svp')
            ->where('svp.video = :video')->setParameter('video', $video)
            ->andWhere('svp.expiredAt > :now')->setParameter('now', new DateTimeImmutable())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param ShareVideoPublic $shareVideoPublic
     * @return void
     */
    public function save(ShareVideoPublic $shareVideoPublic): void
    {
        $em = $this->getEntityManager();
        $em->persist($shareVideoPublic);
        $em->flush();
    }
}
