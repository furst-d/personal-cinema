<?php

namespace App\Controller\V1\Admin\Storage;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\StorageUpgradeFilterRequest;
use App\DTO\Storage\StorageUpgradeQueryRequest;
use App\DTO\Storage\StorageUpgradeRequest;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Storage\StoragePaymentInfo;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Service\Account\AccountService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StorageUpgradeService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/storage/upgrade')]
class StorageUpgradeController extends BasePersonalController
{
    /**
     * @var StorageUpgradeService $storageUpgradeService
     */
    private StorageUpgradeService $storageUpgradeService;

    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @param BaseControllerLocator $locator
     * @param StorageUpgradeService $storageUpgradeService
     * @param AccountService $accountService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StorageUpgradeService $storageUpgradeService,
        AccountService $accountService
    )
    {
        parent::__construct($locator);
        $this->storageUpgradeService = $storageUpgradeService;
        $this->accountService = $accountService;
    }

    #[OA\Get(
        description: "Retrieve a list of storage upgrades.",
        summary: "Get storage upgrades",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgrade::class, groups: [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ], pagination: true, description: "List of storage upgrades")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrades', methods: ['GET'])]
    public function getUpgrades(#[MapQueryString] StorageUpgradeQueryRequest $storageQueryRequest, #[MapQueryString] ?StorageUpgradeFilterRequest $filterRequest): JsonResponse
    {
        $upgrades = $this->storageUpgradeService->getUpgrades($storageQueryRequest, $filterRequest);
        return $this->re->withData($upgrades, [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ]);
    }

    #[OA\Post(
        description: "Create a new storage upgrade.",
        summary: "Create storage upgrade",
        requestBody: new RequestBody(entityClass: StorageUpgradeRequest::class),
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgrade::class, groups: [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ], collection: false, description: "Created storage upgrade")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrade_create', methods: ['POST'])]
    public function createUpgrade(StorageUpgradeRequest $storageRequest): JsonResponse
    {
        try {
            $account = $this->accountService->getAccountByEmail($storageRequest->email);

            $metadata = new StoragePaymentMetadata(
                $account,
                0,
                $storageRequest->size,
                StoragePaymentType::FREE
            );

            $upgrade = $this->storageUpgradeService->createUpgrade($metadata);
            return $this->re->withData($upgrade, [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Batch delete storage upgrades by their ids.",
        summary: "Delete storage upgrades",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgrade::class, groups: [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ], description: "Deleted storage upgrades")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StorageUpgradeService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storage_upgrade_batch_delete', methods: ['DELETE'])]
    public function batchDelete(#[MapQueryString] BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $upgrades = $this->storageUpgradeService->getUpgradesByIds($filter->ids);

            foreach ($upgrades as $upgrade) {
                $this->storageUpgradeService->deleteUpgrade($upgrade);
            }

            return $this->re->withData($upgrades, [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a list of payment types.",
        summary: "Get payment types",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StoragePaymentInfo::class, description: "List of payment types")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('/payment-types', name: 'admin_storage_upgrade_payment_types', methods: ['GET'])]
    public function getPaymentTypes(): JsonResponse
    {
        return $this->re->withData(StoragePaymentType::getAllInfo());
    }
}
