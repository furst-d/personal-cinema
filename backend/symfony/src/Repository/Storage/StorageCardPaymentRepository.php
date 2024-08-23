<?php

namespace App\Repository\Storage;

use App\Entity\Storage\StorageCardPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StorageCardPayment>
 */
class StorageCardPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageCardPayment::class);
    }

    /**
     * @param string $paymentIntent
     * @return StorageCardPayment|null
     */
    public function findByPaymentIntent(string $paymentIntent): ?StorageCardPayment
    {
        return $this->findOneBy(['paymentIntent' => $paymentIntent]);
    }
}
