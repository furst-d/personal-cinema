<?php

namespace App\Repository\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Helper\DTO\SortBy;
use App\Helper\DTO\PaginatorResult;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Folder>
 */
class FolderRepository extends ServiceEntityRepository
{
    use PaginatorHelper;

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
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function findAccountFolders(Account $account, ?Folder $parent, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.owner = :account')->setParameter('account', $account);

        if ($parent) {
            $qb->andWhere('f.parent = :parent')->setParameter('parent', $parent);
        }

        if ($sortBy = $paginatorRequest->getOrderBy()) {
            if ($sortBy === SortBy::NAME) {
                $qb->orderBy('f.name');
            } elseif ($sortBy === SortBy::UPDATE_DATE) {
                $qb->orderBy('f.updatedAt', 'DESC');
            }
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
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
