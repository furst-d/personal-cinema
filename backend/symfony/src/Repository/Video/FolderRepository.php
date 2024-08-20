<?php

namespace App\Repository\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Helper\DTO\SortBy;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
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
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function findFolders(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('f');

        if ($folderData->isDefaultFolder()) {
            $qb->andWhere('f.parent IS NULL')
                ->andwhere('f.owner = :account')->setParameter('account', $account);
        } else {
            if ($folderData->getFolder()) {
                $qb->andWhere('f.parent = :folder')->setParameter('folder', $folderData->getFolder());
            }
        }

        $sort = $paginatorRequest->getSort();
        $order = $paginatorRequest->getOrder()->value;

        switch ($sort) {
            case SortBy::NAME:
                $qb->orderBy('f.name', $order);
                break;
            case SortBy::UPDATE_DATE:
                $qb->orderBy('f.updatedAt', $order);
                break;
            default:
                $qb->orderBy('f.id', $order);
                break;
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param string $phrase
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function searchFolders(Account $account, string $phrase, PaginatorRequest $paginatorRequest)
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.owner = :account')->setParameter('account', $account)
            ->andWhere('f.normalizedName LIKE :phrase')->setParameter('phrase', "%$phrase%")
            ->orderBy('f.updatedAt', 'DESC');

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function findSharedFolders(Account $account, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('f')
            ->join('f.shares', 'sf')
            ->where('sf.account = :account')->setParameter('account', $account);

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
