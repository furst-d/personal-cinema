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
     * @param Folder $folder
     * @return bool
     */
    public function isFolderAlreadyShared(Account $account, Folder $folder): bool
    {
        return $this->createQueryBuilder('sf')
                ->select('COUNT(sf)')
                ->where('sf.folder = :folder')->setParameter('folder', $folder)
                ->andWhere('sf.account = :account')->setParameter('account', $account)
                ->getQuery()->getSingleScalarResult() > 0;
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

    /**
     * @param ShareFolder $shareFolder
     * @return void
     */
    public function save(ShareFolder $shareFolder): void
    {
        $em = $this->getEntityManager();
        $em->persist($shareFolder);
        $em->flush();
    }
}
