<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Storage\StoragePaymentRequest;
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

    #[Route('/personal/storage/upgrade/success', name: 'user_storage_upgrade_success', methods: ['POST'])]
    public function handlePaymentSuccess(Request $request): JsonResponse
    {
//        $requestData = json_decode($request->getContent(), true);
//        $paymentIntentId = $requestData['paymentIntentId'] ?? null;
//
//        if (!$paymentIntentId) {
//            throw new ApiException('Missing payment intent ID');
//        }
//
//        // Ověřte, že Payment Intent je skutečně úspěšný (můžete volat Stripe API zde)
//        $paymentIntent = $this->storageService->retrievePaymentIntent($paymentIntentId);
//        if ($paymentIntent->status !== 'succeeded') {
//            throw new ApiException('Payment not completed');
//        }
//
//        // Logika pro navýšení úložiště pro uživatele
//        $user = $this->getUser(); // Získejte aktuálního uživatele
//        $this->storageService->upgradeUserStorage($user, $paymentIntent->amount); // Logika pro navýšení úložiště
//
        return $this->re->withMessage('Storage upgraded successfully');
    }
}
