<?php

namespace App\Controller\V1\Personal\Storage;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Storage\StorageCheckoutSessionResponse;
use App\DTO\Storage\StoragePaymentRequest;
use App\DTO\Storage\StoragePaymentSuccessRequest;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\PaymentRequiredException;
use App\Exception\UnauthorizedException;
use App\Service\Account\AccountService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Payment\PaymentService;
use App\Service\Storage\StoragePriceService;
use App\Service\Storage\StorageUpgradeService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
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

    #[OA\Get(
        description: "Retrieve user's storage upgrades.",
        summary: "Get user's storage upgrades",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageUpgrade::class, groups: [StorageUpgrade::STORAGE_UPGRADE_READ], description: "List of user's storage upgrades")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_storage_upgrades', methods: ['GET'])]
    public function getUserUpgrades(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);

        return $this->re->withData($account->getStorageUpgrades(), [StorageUpgrade::STORAGE_UPGRADE_READ]);
    }

    #[OA\Get(
        description: "Retrieve Stripe checkout session identifier.",
        summary: "Get checkout session ID",
        tags: [StorageController::TAG],
    )]
    #[ResponseData(entityClass: StorageCheckoutSessionResponse::class, description: "Stripe checkout session ID")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StoragePriceService::INVALID_STORAGE_PRICE_ID))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/payment/session', name: 'user_storage_payment_session', methods: ['GET'])]
    public function createCheckoutSession(Request $request, StoragePaymentRequest $storagePaymentRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $storagePrice = $this->storagePriceService->getPriceById($storagePaymentRequest->storagePriceId);
            $checkoutSession = $this->paymentService->createCheckoutSession($account, $storagePrice);

            return $this->re->withData(new StorageCheckoutSessionResponse($checkoutSession->id));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Create a storage upgrade after successful payment.",
        summary: "Create storage upgrade",
        requestBody: new RequestBody(entityClass: StoragePaymentSuccessRequest::class),
        tags: [StorageController::TAG],
    )]
    #[ResponseMessage(message: "Storage upgraded successfully")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new PaymentRequiredException(PaymentService::PAYMENT_NOT_COMPLETED_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(AccountService::ACCOUNT_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new ConflictException(StorageUpgradeService::UPGRADE_ALREADY_EXISTS_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_storage_upgrade_success', methods: ['POST'])]
    public function handlePaymentSuccess(StoragePaymentSuccessRequest $storagePaymentSuccessRequest): JsonResponse
    {
        try {
            $sessionId = $storagePaymentSuccessRequest->checkoutSessionId;

            $session = $this->paymentService->validatePayment($sessionId);
            $metadata = $this->paymentService->validateMetadata($session->metadata);
            $metadata->setPaymentIntent($session->payment_intent);

            $this->storageUpgradeService->createUpgrade($metadata);

            return $this->re->withMessage('Storage upgraded successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
