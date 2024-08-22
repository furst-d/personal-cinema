<?php

namespace App\Service\Storage;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Storage\Storage;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgrade;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\ConflictException;
use App\Exception\FullStorageException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\TooLargeException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Storage\StorageCardPaymentRepository;
use App\Repository\Storage\StorageRepository;
use App\Repository\Storage\StorageUpgradePriceRepository;
use App\Repository\Storage\StorageUpgradeRepository;
use Exception;

class StorageService
{
    /**
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * @var StorageRepository $storageRepository
     */
    private StorageRepository $storageRepository;

    /**
     * @var StorageUpgradePriceRepository $storageUpgradePriceRepository
     */
    private StorageUpgradePriceRepository $storageUpgradePriceRepository;

    /**
     * @var StorageUpgradeRepository $storageUpgradeRepository
     */
    private StorageUpgradeRepository $storageUpgradeRepository;

    /**
     * @var StorageCardPaymentRepository $storageCardPaymentRepository
     */
    private StorageCardPaymentRepository $storageCardPaymentRepository;

    /**
     * @param SettingsRepository $settingsRepository
     * @param StorageRepository $storageRepository
     * @param StorageUpgradePriceRepository $storageUpgradePriceRepository
     * @param StorageUpgradeRepository $storageUpgradeRepository
     * @param StorageCardPaymentRepository $storageCardPaymentRepository
     */
    public function __construct(
        SettingsRepository $settingsRepository,
        StorageRepository $storageRepository,
        StorageUpgradePriceRepository $storageUpgradePriceRepository,
        StorageUpgradeRepository $storageUpgradeRepository,
        StorageCardPaymentRepository $storageCardPaymentRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->storageRepository = $storageRepository;
        $this->storageUpgradePriceRepository = $storageUpgradePriceRepository;
        $this->storageUpgradeRepository = $storageUpgradeRepository;
        $this->storageCardPaymentRepository = $storageCardPaymentRepository;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filterRequest
     * @return PaginatorResult<Storage>
     */
    public function getStorages(PaginatorRequest $paginatorRequest, ?FilterRequest $filterRequest): PaginatorResult
    {
        return $this->storageRepository->getStorages($paginatorRequest, $filterRequest);
    }

    /**
     * @param int $id
     * @return Storage
     * @throws NotFoundException
     */
    public function getStorageById(int $id): Storage
    {
        $storage = $this->storageRepository->find($id);

        if (!$storage) {
            throw new NotFoundException('Storage not found');
        }

        return $storage;
    }

    /**
     * @param Storage $storage
     * @param int $fileSize
     * @return void
     * @throws TooLargeException|FullStorageException
     */
    public function checkStorageBeforeUpload(Storage $storage, int $fileSize): void
    {
        if ($this->hasExceededStorageLimit($storage->getMaxStorage(), $storage->getUsedStorage() + $fileSize)) {
            throw new FullStorageException('File exceeds storage limit');
        }

        if ($this->hasExceededFileLimit($fileSize)) {
            $maxFile = $this->settingsRepository->getMaxFileSize();
            $maxFile = substr($maxFile, 0, -2) . ' ' . substr($maxFile, -2);
            throw new TooLargeException('File too large', ['maxFileSize' => $maxFile]);
        }
    }

    /**
     * @return int
     */
    public function getDefaultUserStorageLimit(): int
    {
        return $this->convertSizeToBytes($this->settingsRepository->getDefaultUserStorageLimit());
    }

    /**
     * @return array
     */
    public function getPrices(): array
    {
        return $this->storageUpgradePriceRepository->getAllBySize();
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
     * @param int $storagePriceId
     * @return StorageUpgradePrice
     * @throws NotFoundException
     */
    public function getPriceById(int $storagePriceId): StorageUpgradePrice
    {
        /** @var StorageUpgradePrice $price */
        $price = $this->storageUpgradePriceRepository->find($storagePriceId);

        if (!$price) {
            throw new NotFoundException('Invalid storage price ID');
        }

        return $price;
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
     * @param Storage $storage
     * @param int $maxStorage
     * @return void
     */
    public function updateMaxStorage(Storage $storage, int $maxStorage): void
    {
        $storage->setMaxStorage($maxStorage);
        $this->storageRepository->save($storage);
    }


    /**
     * Convert a size string (e.g., "500MB", "10GB") to bytes.
     *
     * @param string $size
     * @return int
     */
    private function convertSizeToBytes(string $size): int
    {
        $size = trim($size);
        $unit = strtoupper(substr($size, -2));
        $value = (int) substr($size, 0, -2);

        return match ($unit) {
            'KB' => $value * 1024,
            'MB' => $value * 1024 * 1024,
            'GB' => $value * 1024 * 1024 * 1024,
            'TB' => $value * 1024 * 1024 * 1024 * 1024,
            default => $value,
        };
    }

    /**
     * @param int $maxStorage
     * @param int $usedStorage
     * @return bool
     */
    private function hasExceededStorageLimit(int $maxStorage, int $usedStorage): bool
    {
        return $usedStorage > $maxStorage;
    }

    /**
     * @return int
     */
    private function getMaxFileSize(): int
    {
        return $this->convertSizeToBytes($this->settingsRepository->getMaxFileSize());
    }

    /**
     * @param int $fileSize
     * @return bool
     */
    private function hasExceededFileLimit(int $fileSize): bool
    {
        return $fileSize > $this->getMaxFileSize();
    }
}
