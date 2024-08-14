<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Storage\StoragePaymentRequest;
use App\DTO\Storage\StoragePaymentSuccessRequest;
use App\Exception\ApiException;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Payment\PaymentService;
use App\Service\Storage\StorageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage/upgrade')]
class StorageUpgradeController extends BasePersonalController
{
    /**
     * @var StorageService $storageService
     */
    private StorageService $storageService;

    /**
     * @var PaymentService $paymentService
     */
    private PaymentService $paymentService;

    /**
     * @param BaseControllerLocator $locator
     * @param StorageService $storageService
     * @param PaymentService $paymentService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StorageService $storageService,
        PaymentService $paymentService
    )
    {
        parent::__construct($locator);
        $this->storageService = $storageService;
        $this->paymentService = $paymentService;
    }

    #[Route('/payment/session', name: 'user_storage_payment_session', methods: ['GET'])]
    public function createCheckoutSession(Request $request, StoragePaymentRequest $storagePaymentRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $storagePrice = $this->storageService->getPriceById($storagePaymentRequest->storagePriceId);
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
            $account = $this->getAccount($request);
            $sessionId = $storagePaymentSuccessRequest->checkoutSessionId;

            $session = $this->paymentService->validatePayment($sessionId);
            $metadata = $this->paymentService->validateMetadata($session->metadata);

            $this->storageService->createUpgrade($account, $metadata, $sessionId);

            return $this->re->withMessage('Storage upgraded successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
