<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\Entity\Storage\StorageUpgradePrice;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StoragePriceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage/upgrade/price')]
class StorageUpgradePriceController extends BasePersonalController
{
    /**
     * @var StoragePriceService $storagePriceService
     */
    private StoragePriceService $storagePriceService;

    /**
     * @param BaseControllerLocator $locator
     * @param StoragePriceService $storagePriceService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StoragePriceService $storagePriceService
    )
    {
        parent::__construct($locator);
        $this->storagePriceService = $storagePriceService;
    }

    #[Route('', name: 'user_storage_prices', methods: ['GET'])]
    public function getStoragePrices(): JsonResponse
    {
        return $this->re->withData($this->storagePriceService->getPricesBySize(), [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_READ]);
    }
}
