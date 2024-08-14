<?php

namespace App\Repository\Storage;

use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgradePrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorageCardPayment>
 */
class StorageUpgradePriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageUpgradePrice::class);
    }

    /**
     * @return StorageUpgradePrice[]
     */
    public function getAllBySize(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.size', 'ASC')
            ->getQuery()->getResult();
    }
}
