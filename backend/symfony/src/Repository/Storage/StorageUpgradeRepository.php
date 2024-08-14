<?php

namespace App\Repository\Storage;

use App\Entity\Storage\StorageUpgrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorageUpgrade>
 */
class StorageUpgradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageUpgrade::class);
    }

    /**
     * @param StorageUpgrade $storageUpgrade
     * @return void
     */
    public function save(StorageUpgrade $storageUpgrade): void
    {
        $em = $this->getEntityManager();
        $em->persist($storageUpgrade);
        $em->flush();
    }
}
