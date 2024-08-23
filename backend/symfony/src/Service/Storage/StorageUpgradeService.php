<?php

namespace App\Service\Storage;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Storage\StorageUpgradeQueryRequest;
use App\Entity\Account\Account;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
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
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filterRequest
     * @return PaginatorResult<StorageUpgrade>
     */
    public function getUpgrades(PaginatorRequest $paginatorRequest, ?FilterRequest $filterRequest): PaginatorResult
    {
        return $this->storageUpgradeRepository->getUpgrades($paginatorRequest, $filterRequest);
    }

    /**
     * @param StoragePaymentMetadata $metadata
     * @return StorageUpgrade
     * @throws ConflictException
     */
    public function createUpgrade(StoragePaymentMetadata $metadata): StorageUpgrade
    {
        if ($metadata->getStripeSessionId() && $this->getStorageCardPaymentBySession($metadata->getStripeSessionId())) {
            throw new ConflictException('Upgrade already exists');
        }

        $storageUpgrade = new StorageUpgrade(
            $metadata->getAccount(),
            $metadata->getPriceCzk(),
            $metadata->getSize(),
            $metadata->getType(),
            $metadata->getStripeSessionId()
        );

        $storage = $metadata->getAccount()->getStorage();
        $storage->setMaxStorage($storage->getMaxStorage() + $metadata->getSize());

        $this->storageUpgradeRepository->save($storageUpgrade);

        return $storageUpgrade;
    }

    /**
     * @param string $sessionId
     * @return StorageCardPayment|null
     */
    public function getStorageCardPaymentBySession(string $sessionId): ?StorageCardPayment
    {
        return $this->storageCardPaymentRepository->findBySessionId($sessionId);
    }

    /**
     * @param int[] $ids
     * @return StorageUpgrade[]
     * @throws NotFoundException
     */
    public function getUpgradesByIds(array $ids): array
    {
        $upgrades = $this->storageUpgradeRepository->findByIds($ids);

        if (count($upgrades) !== count($ids)) {
            throw new NotFoundException('Some upgrades not found.');
        }

        return $upgrades;
    }

    /**
     * @param StorageUpgrade $upgrade
     * @return void
     */
    public function deleteUpgrade(StorageUpgrade $upgrade): void
    {
        $storage = $upgrade->getAccount()->getStorage();
        $storage->setMaxStorage($storage->getMaxStorage() - $upgrade->getSize());

        $this->storageUpgradeRepository->delete($upgrade);
    }
}
