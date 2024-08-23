<?php

namespace App\Controller\V1\Admin\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\StorageUpgradeFilterRequest;
use App\DTO\Storage\StorageUpgradeQueryRequest;
use App\DTO\Storage\StorageUpgradeRequest;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ApiException;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Service\Account\AccountService;
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

    #[Route('', name: 'admin_storage_upgrades', methods: ['GET'])]
    public function getUpgrades(StorageUpgradeQueryRequest $storageQueryRequest, ?StorageUpgradeFilterRequest $filterRequest): JsonResponse
    {
        $upgrades = $this->storageUpgradeService->getUpgrades($storageQueryRequest, $filterRequest);
        return $this->re->withData($upgrades, [StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ]);
    }

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

    #[Route('', name: 'admin_storage_upgrade_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
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

    #[Route('/payment-types', name: 'admin_storage_upgrade_payment_types', methods: ['GET'])]
    public function getPaymentTypes(): JsonResponse
    {
        return $this->re->withData(StoragePaymentType::getAllInfo());
    }
}
