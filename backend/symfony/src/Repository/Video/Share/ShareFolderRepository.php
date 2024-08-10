<?php

namespace App\Repository\Video\Share;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareFolder;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
use App\Repository\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShareFolder>
 */
class ShareFolderRepository extends ServiceEntityRepository
{
    use PaginatorHelper;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareFolder::class);
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @return bool
     */
    public function hasSharedFolderAccess(Account $account, Folder $folder): bool
    {
        return $this->createQueryBuilder('sf')
            ->select('COUNT(sf)')
            ->where('sf.folder = :folder')->setParameter('folder', $folder)
            ->andWhere('sf.account = :account')->setParameter('account', $account)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Account $account
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function findSharedFolders(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb = $this->createQueryBuilder('sf')
            ->where('sf.account = :account')->setParameter('account', $account);

        if ($folderData->isDefaultFolder()) {
            $qb->andWhere('sf.folder.parent IS NULL');
        } else {
            if ($folderData->getFolder()) {
                $qb->andWhere('sf.folder.parent = :folder')->setParameter('folder', $folderData->getFolder());
            }
        }

        return $this->getPaginatorResult($qb, $paginatorRequest);
    }

    /**
     * @param ShareFolder $videoShare
     * @return void
     */
    public function delete(ShareFolder $videoShare): void
    {
        $em = $this->getEntityManager();
        $em->remove($videoShare);
        $em->flush();
    }
}
