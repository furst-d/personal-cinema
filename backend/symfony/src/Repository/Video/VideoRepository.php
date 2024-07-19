<?php

namespace App\Repository\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Video;
use App\Helper\Paginator\PaginatorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
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
     * @param Account $account
     * @param Folder|null $folder
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Video>
     */
    public function findAccountVideos(Account $account, ?Folder $folder, ?int $limit, ?int $offset): PaginatorResult
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.account = :account')->setParameter('account', $account)
            ->andWhere('v.isDeleted = false');

        if ($folder) {
            $qb->andWhere('v.folder = :folder')->setParameter('folder', $folder);
        }

        if (!is_null($limit) && !is_null($offset)) {
            $qb->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        $paginator = new Paginator($qb);
        $totalItems = $paginator->count();

        return new PaginatorResult(iterator_to_array($paginator), $totalItems);
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
