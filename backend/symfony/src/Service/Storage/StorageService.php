<?php

namespace App\Service\Storage;

use App\Entity\Storage\Storage;
use App\Exception\FullStorageException;
use App\Exception\TooLargeException;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Storage\StorageUpgradePriceRepository;

class StorageService
{
    /**
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * @var StorageUpgradePriceRepository $storageUpgradePriceRepository
     */
    private StorageUpgradePriceRepository $storageUpgradePriceRepository;

    /**
     * @param SettingsRepository $settingsRepository
     * @param StorageUpgradePriceRepository $storageUpgradePriceRepository
     */
    public function __construct(
        SettingsRepository $settingsRepository,
        StorageUpgradePriceRepository $storageUpgradePriceRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->storageUpgradePriceRepository = $storageUpgradePriceRepository;
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
