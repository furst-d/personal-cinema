<?php

namespace App\Repository\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Helper\Folder\FolderDeletionMode;
use App\Helper\Paginator\PaginatorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Folder>
 */
class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    /**
     * @param Folder $folder
     * @return void
     */
    public function save(Folder $folder): void
    {
        $em = $this->getEntityManager();
        $em->persist($folder);
        $em->flush();
    }

    /**
     * @param Account $account
     * @param Folder|null $parent
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Folder>
     */
    public function findAccountFolders(Account $account, ?Folder $parent, ?int $limit, ?int $offset): PaginatorResult
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.owner = :account')->setParameter('account', $account);

        if ($parent) {
            $qb->andWhere('f.parent = :parent')->setParameter('parent', $parent);
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
     * @param Folder $folder
     * @return void
     */
    public function delete(Folder $folder): void
    {
        $em = $this->getEntityManager();

        $this->deleteRecursive($folder, $em);
        $em->flush();
    }

    /**
     * @param Folder $folder
     * @param EntityManagerInterface $em
     * @return void
     */
    private function deleteRecursive(Folder $folder, EntityManagerInterface $em): void
    {
        // Recursively delete subfolders
        foreach ($folder->getSubFolders() as $subFolder) {
            $this->deleteRecursive($subFolder, $em);
        }

        // Delete or nullify all videos in the folder
        foreach ($folder->getVideos() as $video) {
            $em->remove($video);
        }

        // Finally, remove the folder itself
        $em->remove($folder);
    }
}
