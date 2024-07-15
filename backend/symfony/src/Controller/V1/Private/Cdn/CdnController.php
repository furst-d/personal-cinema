<?php

namespace App\Controller\V1\Private\Cdn;

use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\CdnRequest;
use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
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
    public function actionNotification(Request $request, CdnRequest $cdnRequest): JsonResponse
    {
        try {
            $this->authService->authCdn($request);
            $this->cdnService->synchronizeVideo($cdnRequest->video);
            return $this->re->withMessage('Notification processed.');
        } catch (UnauthorizedException|BadRequestException|NotFoundException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/thumb', name: 'cdn_thumb', methods: ['POST'])]
    public function actionThumb(Request $request): JsonResponse
    {
        try {
            $this->authService->authCdn($request);
            return $this->re->withMessage('Thumb processed.');
        } catch (UnauthorizedException $e) {
            return $this->re->withException($e);
        }
    }
}
