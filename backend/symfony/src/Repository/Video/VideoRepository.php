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

        if ($limit !== null && $offset !== null) {
            $qb->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        $paginator = new Paginator($qb);
        $totalItems = $paginator->count();

        return new PaginatorResult(iterator_to_array($paginator), $totalItems);
    }
}
