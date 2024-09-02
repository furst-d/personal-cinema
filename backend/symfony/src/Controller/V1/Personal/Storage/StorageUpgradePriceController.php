<?php

namespace App\Controller\V1\Personal\Storage;

use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Controller\V1\Personal\BasePersonalController;
use App\Entity\Storage\StorageUpgrade;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\InternalException;
use App\Exception\UnauthorizedException;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StoragePriceService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
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

    #[OA\Get(
        description: "Retrieve storage upgrade prices.",
        summary: "Get storage upgrade prices",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, groups: [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_READ], description: "List of storage upgrade prices")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_storage_prices', methods: ['GET'])]
    public function getStoragePrices(): JsonResponse
    {
        return $this->re->withData($this->storagePriceService->getPricesBySize(), [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_READ]);
    }
}
