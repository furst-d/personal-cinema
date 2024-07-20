<?php

namespace App\Repository\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Video;
use App\Helper\Paginator\PaginatorResult;
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
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Video>
     */
    public function findVideos(?Account $account, ?Folder $folder, ?int $limit, ?int $offset): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.isDeleted = false');

        if ($account) {
            $qb->andWhere('v.account = :account')->setParameter('account', $account);
        }

        if ($folder) {
            $qb->andWhere('v.folder = :folder')->setParameter('folder', $folder);
        }

        return $this->getPaginatorResult($qb, $limit, $offset);
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
