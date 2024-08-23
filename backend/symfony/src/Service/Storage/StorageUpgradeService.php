<?php

namespace App\Service\Storage;

use App\Entity\Account\Account;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ConflictException;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Storage\StorageCardPaymentRepository;
use App\Repository\Storage\StorageUpgradeRepository;

class StorageUpgradeService
{
    /**
     * @var StorageUpgradeRepository $storageUpgradeRepository
     */
    private StorageUpgradeRepository $storageUpgradeRepository;

    /**
     * @var StorageCardPaymentRepository $storageCardPaymentRepository
     */
    private StorageCardPaymentRepository $storageCardPaymentRepository;

    /**
     * @param StorageUpgradeRepository $storageUpgradeRepository
     * @param StorageCardPaymentRepository $storageCardPaymentRepository
     */
    public function __construct(
        StorageUpgradeRepository $storageUpgradeRepository,
        StorageCardPaymentRepository $storageCardPaymentRepository
    )
    {
        $this->storageUpgradeRepository = $storageUpgradeRepository;
        $this->storageCardPaymentRepository = $storageCardPaymentRepository;
    }

    /**
     * @param Account $account
     * @param StoragePaymentMetadata $metadata
     * @param string $sessionId
     * @return void
     * @throws ConflictException
     */
    public function createUpgrade(Account $account, StoragePaymentMetadata $metadata, string $sessionId): void
    {
        if ($this->getStorageCardPaymentBySession($sessionId)) {
            throw new ConflictException('Upgrade already exists');
        }

        $storageUpgrade = new StorageUpgrade(
            $account,
            $metadata->getPriceCzk(),
            $metadata->getSize(),
            StoragePaymentType::CARD,
            $sessionId
        );

        $storage = $account->getStorage();
        $storage->setMaxStorage($storage->getMaxStorage() + $metadata->getSize());

        $this->storageUpgradeRepository->save($storageUpgrade);
    }

    /**
     * @param string $sessionId
     * @return StorageCardPayment|null
     */
    public function getStorageCardPaymentBySession(string $sessionId): ?StorageCardPayment
    {
        return $this->storageCardPaymentRepository->findBySessionId($sessionId);
    }
}
