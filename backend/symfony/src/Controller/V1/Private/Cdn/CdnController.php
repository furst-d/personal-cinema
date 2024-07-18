<?php

namespace App\Controller\V1\Private\Cdn;

use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\CdnNotificationRequest;
use App\DTO\Video\CdnThumbnailRequest;
use App\Exception\ApiException;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Locator\BaseControllerLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/cdn')]
class CdnController extends BasePrivateController
{
    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @param BaseControllerLocator $locator
     * @param AuthService $authService
     * @param CdnService $cdnService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AuthService $authService,
        CdnService $cdnService
    )
    {
        parent::__construct($locator, $authService);
        $this->cdnService = $cdnService;
    }

    #[Route('/notification', name: 'cdn_notification', methods: ['POST'])]
    public function actionNotification(Request $request, CdnNotificationRequest $notificationRequest): JsonResponse
    {
        try {
            $this->authService->authCdn($request);
            $this->cdnService->synchronizeVideo($notificationRequest->video);
            return $this->re->withMessage('Notification processed.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/thumb', name: 'cdn_thumb', methods: ['POST'])]
    public function actionThumb(Request $request, CdnThumbnailRequest $thumbnailRequest): JsonResponse
    {
        try {
            $this->authService->authCdn($request);
            $this->cdnService->synchronizeThumbnail($thumbnailRequest->video, $thumbnailRequest->thumbs);
            return $this->re->withMessage('Thumb processed.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
