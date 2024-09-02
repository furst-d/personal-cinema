<?php

namespace App\Controller\V1\Private\Cdn;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\CdnNotificationRequest;
use App\DTO\Video\CdnThumbnailRequest;
use App\DTO\Video\FolderRequest;
use App\Entity\Video\Folder;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
use App\Service\Video\VideoService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
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

    public const TAG = 'private/cdn';

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

    #[OA\Post(
        description: "Is used by CDN to notify about changes in video, e.g. conversions generated.",
        summary: "Process notification from CDN",
        requestBody: new RequestBody(entityClass: CdnNotificationRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: 'Notification processed.')]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Post(
        description: "Is used by CDN to notify about generated thumbnails.",
        summary: "Process thumbnails from CDN",
        requestBody: new RequestBody(entityClass: CdnThumbnailRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: 'Thumb processed.')]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/thumb', name: 'cdn_thumb', methods: ['POST'])]
    public function actionThumb(Request $request, CdnThumbnailRequest $thumbnailRequest): JsonResponse
    {
        try {
            $this->authService->authCdn($request);
            $this->cdnService->synchronizeThumbnail($thumbnailRequest->video);
            return $this->re->withMessage('Thumb processed.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
