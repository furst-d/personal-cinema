<?php

namespace App\Service\Storage;

use App\DTO\PaginatorRequest;
use App\DTO\Storage\StorageUpgradePriceRequest;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Repository\Storage\StorageUpgradePriceRepository;

class StoragePriceService
{
    /**
     * @var StorageUpgradePriceRepository $storageUpgradePriceRepository
     */
    private StorageUpgradePriceRepository $storageUpgradePriceRepository;

    public const INVALID_STORAGE_PRICE_ID = 'Invalid storage price ID';

    /**
     * @param StorageUpgradePriceRepository $storageUpgradePriceRepository
     */
    public function __construct(StorageUpgradePriceRepository $storageUpgradePriceRepository)
    {
        $this->storageUpgradePriceRepository = $storageUpgradePriceRepository;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<StorageUpgradePrice>
     */
    public function getPrices(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->storageUpgradePriceRepository->getPrices($paginatorRequest);
    }

    /**
     * @return StorageUpgradePrice[]
     */
    public function getPricesBySize(): array
    {
        return $this->storageUpgradePriceRepository->getAllBySize();
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
            throw new NotFoundException(self::INVALID_STORAGE_PRICE_ID);
        }

        return $price;
    }

    /**
     * @param int[] $ids
     * @return StorageUpgradePrice[]
     * @throws NotFoundException
     */
    public function getPricesByIds(array $ids): array
    {
        $prices = $this->storageUpgradePriceRepository->findByIds($ids);

        if (count($prices) !== count($ids)) {
            throw new NotFoundException('Some storage prices not found');
        }

        return $prices;
    }

    /**
     * @param StorageUpgradePriceRequest $storageRequest
     * @return StorageUpgradePrice
     */
    public function createPrice(StorageUpgradePriceRequest $storageRequest): StorageUpgradePrice
    {
        $price = new StorageUpgradePrice($storageRequest->size, $storageRequest->priceCzk);
        $price->setPercentageDiscount($storageRequest->percentageDiscount);
        $price->setDiscountExpirationAt($storageRequest->discountExpirationAt);
        $this->storageUpgradePriceRepository->save($price);
        return $price;
    }

    /**
     * @param StorageUpgradePrice $price
     * @param StorageUpgradePriceRequest $storageRequest
     * @return void
     */
    public function updatePrice(StorageUpgradePrice $price, StorageUpgradePriceRequest $storageRequest): void
    {
        $price->setPriceCzk($storageRequest->priceCzk);
        $price->setSize($storageRequest->size);
        $price->setPercentageDiscount($storageRequest->percentageDiscount);
        $price->setDiscountExpirationAt($storageRequest->discountExpirationAt);
        $this->storageUpgradePriceRepository->save($price);
    }

    /**
     * @param StorageUpgradePrice $price
     * @return void
     */
    public function deletePrice(StorageUpgradePrice $price): void
    {
        $this->storageUpgradePriceRepository->delete($price);
    }
}
