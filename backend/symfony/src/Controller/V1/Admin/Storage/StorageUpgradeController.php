<?php

namespace App\Controller\V1\Admin\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Storage\StorageUpgradePriceQueryRequest;
use App\DTO\Storage\StorageUpgradePriceRequest;
use App\DTO\Storage\StorageUpgradeQueryRequest;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\ApiException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StorageUpgradeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/storage/upgrade')]
class StorageUpgradeController extends BasePersonalController
{
    /**
     * @var StorageUpgradeService $storageUpgradeService
     */
    private StorageUpgradeService $storageUpgradeService;

    /**
     * @param BaseControllerLocator $locator
     * @param StorageUpgradeService $storageUpgradeService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StorageUpgradeService $storageUpgradeService
    )
    {
        parent::__construct($locator);
        $this->storageUpgradeService = $storageUpgradeService;
    }

//    #[Route('', name: 'admin_storage_upgrade_prices', methods: ['GET'])]
//    public function getPrices(StorageUpgradeQueryRequest $storageQueryRequest): JsonResponse
//    {
//        $prices = $this->storagePriceService->getPrices($storageQueryRequest);
//        return $this->re->withData($prices, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
//    }
//
//    #[Route('', name: 'admin_storage_upgrade_prices_batch_delete', methods: ['DELETE'])]
//    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
//    {
//        try {
//            $prices = $this->storagePriceService->getPricesByIds($filter->ids);
//
//            foreach ($prices as $price) {
//                $this->storagePriceService->deletePrice($price);
//            }
//
//            return $this->re->withData($prices);
//        } catch (ApiException $e) {
//            return $this->re->withException($e);
//        }
//    }
//
//    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price', methods: ['GET'])]
//    public function getPriceDetail(int $id): JsonResponse
//    {
//        try {
//            $price = $this->storagePriceService->getPriceById($id);
//            return $this->re->withData($price, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
//        } catch (ApiException $e) {
//            return $this->re->withException($e);
//        }
//    }
//
//    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price_update', methods: ['PUT'])]
//    public function updatePrice(int $id, StorageUpgradePriceRequest $storageRequest): JsonResponse
//    {
//        try {
//            $price = $this->storagePriceService->getPriceById($id);
//            $this->storagePriceService->updatePrice($price, $storageRequest);
//            return $this->re->withData($price, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
//        } catch (ApiException $e) {
//            return $this->re->withException($e);
//        }
//    }
//
//    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price_delete', methods: ['DELETE'])]
//    public function deleteSetting(int $id): JsonResponse
//    {
//        try {
//            $price = $this->storagePriceService->getPriceById($id);
//            $this->storagePriceService->deletePrice($price);
//            return $this->re->withMessage('Price deleted successfully');
//        } catch (ApiException $e) {
//            return $this->re->withException($e);
//        }
//    }
}
