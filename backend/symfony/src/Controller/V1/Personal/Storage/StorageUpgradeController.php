<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Storage\StoragePaymentRequest;
use App\DTO\Storage\StoragePaymentSuccessRequest;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ApiException;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Payment\PaymentService;
use App\Service\Storage\StoragePriceService;
use App\Service\Storage\StorageUpgradeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage/upgrade')]
class StorageUpgradeController extends BasePersonalController
{
    /**
     * @var StoragePriceService $storagePriceService
     */
    private StoragePriceService $storagePriceService;

    /**
     * @var StorageUpgradeService $storageUpgradeService
     */
    private StorageUpgradeService $storageUpgradeService;

    /**
     * @var PaymentService $paymentService
     */
    private PaymentService $paymentService;

    /**
     * @param BaseControllerLocator $locator
     * @param StoragePriceService $storagePriceService
     * @param StorageUpgradeService $storageUpgradeService
     * @param PaymentService $paymentService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StoragePriceService $storagePriceService,
        StorageUpgradeService $storageUpgradeService,
        PaymentService $paymentService
    )
    {
        parent::__construct($locator);
        $this->storagePriceService = $storagePriceService;
        $this->storageUpgradeService = $storageUpgradeService;
        $this->paymentService = $paymentService;
    }

    #[Route('', name: 'user_storage_upgrades', methods: ['GET'])]
    public function getUserUpgrades(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);

        return $this->re->withData($account->getStorageUpgrades(), [StorageUpgrade::STORAGE_UPGRADE_READ]);
    }

    #[Route('/payment/session', name: 'user_storage_payment_session', methods: ['GET'])]
    public function createCheckoutSession(Request $request, StoragePaymentRequest $storagePaymentRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $storagePrice = $this->storagePriceService->getPriceById($storagePaymentRequest->storagePriceId);
            $checkoutSession = $this->paymentService->createCheckoutSession($account, $storagePrice);

            return $this->re->withData(['checkoutSessionId' => $checkoutSession->id]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('', name: 'user_storage_upgrade_success', methods: ['POST'])]
    public function handlePaymentSuccess(Request $request, StoragePaymentSuccessRequest $storagePaymentSuccessRequest): JsonResponse
    {
        try {
            $sessionId = $storagePaymentSuccessRequest->checkoutSessionId;

            $session = $this->paymentService->validatePayment($sessionId);
            $metadata = $this->paymentService->validateMetadata($session->metadata);

            $this->storageUpgradeService->createUpgrade($metadata);

            return $this->re->withMessage('Storage upgraded successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
