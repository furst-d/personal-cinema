<?php

namespace App\Controller\V1\Front;

use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Controller\ApiController;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Service\Account\SessionService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/share')]
class ShareVideoPublicController extends ApiController
{
    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var SessionService $sessionService
     */
    private SessionService $sessionService;

    public const TAG = 'public/videos';

    /**
     * @param VideoService $videoService
     * @param ShareService $shareService
     * @param SessionService $sessionService
     */
    public function __construct(
        VideoService $videoService,
        ShareService $shareService,
        SessionService $sessionService,
    )
    {
        $this->videoService = $videoService;
        $this->shareService = $shareService;
        $this->sessionService = $sessionService;
    }

    #[OA\Get(
        description: "Retrieve a video detail when accessing through public share link.",
        summary: "Get video detail by a public share link",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEO_PUBLIC_READ], collection: false, description: "Video detail")]
    #[ResponseError(exception: new ForbiddenException(ShareService::NO_PERMISSION_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(ShareService::VIDEO_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Route('/{hash}', name: 'public_shared_video_detail', methods: ['GET'])]
    public function getVideoDetail(Request $request, string $hash): JsonResponse
    {
        try {
            $sessionId = $this->sessionService->generate($request);

            $videoShare = $this->shareService->getPublicVideoByHash($hash, $sessionId);
            $video = $videoShare->getVideo();
            $this->videoService->addPublicVideoUrlToVideo($video);

            return $this->re->withData($video, [Video::VIDEO_PUBLIC_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
