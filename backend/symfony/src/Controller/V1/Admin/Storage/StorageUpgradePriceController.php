<?php

namespace App\Controller\V1\Admin\Storage;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Storage\StorageUpgradePriceQueryRequest;
use App\DTO\Storage\StorageUpgradePriceRequest;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StoragePriceService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/storage/upgrade/price')]
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
        description: "Retrieve a list of storage upgrade prices.",
        summary: "Get storage upgrade prices",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, groups: [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ], pagination: true, description: "List of storage upgrade prices")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrade_prices', methods: ['GET'])]
    public function getPrices(#[MapQueryString] StorageUpgradePriceQueryRequest $storageQueryRequest): JsonResponse
    {
        $prices = $this->storagePriceService->getPrices($storageQueryRequest);
        return $this->re->withData($prices, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
    }

    #[OA\Post(
        description: "Create a new storage upgrade price.",
        summary: "Create storage upgrade price",
        requestBody: new RequestBody(entityClass: StorageUpgradePriceRequest::class),
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, groups: [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ], collection: false, description: "Created storage upgrade price")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrade_price_create', methods: ['POST'])]
    public function createPrice(StorageUpgradePriceRequest $storageRequest): JsonResponse
    {
        $price = $this->storagePriceService->createPrice($storageRequest);
        return $this->re->withData($price, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
    }

    #[OA\Delete(
        description: "Batch delete storage upgrade prices by their ids.",
        summary: "Delete storage upgrade prices",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, description: "Deleted storage upgrade prices")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StoragePriceService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrade_prices_batch_delete', methods: ['DELETE'])]
    public function batchDelete(#[MapQueryString] BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $prices = $this->storagePriceService->getPricesByIds($filter->ids);

            foreach ($prices as $price) {
                $this->storagePriceService->deletePrice($price);
            }

            return $this->re->withData($prices);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a storage upgrade price by id.",
        summary: "Get storage upgrade price",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, groups: [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ], collection: false, description: "Storage upgrade size detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StoragePriceService::INVALID_STORAGE_PRICE_ID))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price', methods: ['GET'])]
    public function getPriceDetail(int $id): JsonResponse
    {
        try {
            $price = $this->storagePriceService->getPriceById($id);
            return $this->re->withData($price, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Updates storage upgrade price by id.",
        summary: "Update storage upgrade price",
        requestBody: new RequestBody(entityClass: StorageUpgradePriceRequest::class),
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgradePrice::class, groups: [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ], collection: false, description: "Updated storage upgrade price")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StoragePriceService::INVALID_STORAGE_PRICE_ID))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price_update', methods: ['PUT'])]
    public function updatePrice(int $id, StorageUpgradePriceRequest $storageRequest): JsonResponse
    {
        try {
            $price = $this->storagePriceService->getPriceById($id);
            $this->storagePriceService->updatePrice($price, $storageRequest);
            return $this->re->withData($price, [StorageUpgradePrice::STORAGE_UPGRADE_PRICE_ADMIN_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete storage upgrade price by ids.",
        summary: "Delete storage upgrade price",
        tags: [StorageController::TAG],
    )]
    #[ResponseMessage(message: "Price deleted successfully")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StoragePriceService::INVALID_STORAGE_PRICE_ID))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_storage_upgrade_price_delete', methods: ['DELETE'])]
    public function deletePrice(int $id): JsonResponse
    {
        try {
            $price = $this->storagePriceService->getPriceById($id);
            $this->storagePriceService->deletePrice($price);
            return $this->re->withMessage('Price deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
