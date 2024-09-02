<?php

namespace App\Controller\V1\Private\Video;

use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseFile;
use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\QualityRequest;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\File\MimeType;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\SessionService;
use App\Service\Auth\AuthService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\ManifestService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/videos')]
class VideoController extends BasePrivateController
{
    /**
     * @var ManifestService $manifestService
     */
    private ManifestService $manifestService;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var SessionService $sessionService
     */
    private SessionService $sessionService;

    public const TAG = 'private/videos';

    /**
     * @param BaseControllerLocator $locator
     * @param AuthService $authService
     * @param ManifestService $manifestService
     * @param ShareService $shareService
     * @param SessionService $sessionService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AuthService $authService,
        ManifestService $manifestService,
        ShareService $shareService,
        SessionService $sessionService,
    )
    {
        parent::__construct($locator, $authService);
        $this->manifestService = $manifestService;
        $this->shareService = $shareService;
        $this->sessionService = $sessionService;
    }

    #[OA\Get(
        description: "Retrieve a signed HLS manifest to a video from CDN. Works as a proxy to the CDN. Manifest is a dynamically generated and includes a signed links. If a quality is specified, the manifest will be generated for that quality and will includes signed links to a video segments. Otherwise, the manifest will be generated and will include signed links to this proxy for every available quality.",
        summary: "Retrieve a signed HLS manifest from CDN for a video",
        tags: [self::TAG],
    )]
    #[ResponseFile(mimeType: MimeType::APPLICATION_MPEGURL, description: "HLS manifest")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/url', name: 'video_manifest', methods: ['GET'])]
    public function getManifest(Request $request, #[MapQueryString] QualityRequest $qualityRequest): Response
    {
        try {
            $video = $this->authService->authVideo($request, JwtUsage::USAGE_VIDEO_ACCESS);
            $manifestContent = $this->manifestService->getManifest($video, $qualityRequest->quality);

            return new Response($manifestContent, 200, ['Content-Type' => MimeType::APPLICATION_MPEGURL->value]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a signed HLS manifest to a shared video from CDN. Works as a proxy to the CDN. Manifest is a dynamically generated and includes a signed links. If a quality is specified, the manifest will be generated for that quality and will includes signed links to a video segments. Otherwise, the manifest will be generated and will include signed links to this proxy for every available quality.",
        summary: "Retrieve a signed HLS manifest from CDN for a shared video",
        tags: [self::TAG],
    )]
    #[ResponseFile(mimeType: MimeType::APPLICATION_MPEGURL, description: "HLS manifest")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/share/url', name: 'public_video_manifest', methods: ['GET'])]
    public function getShareManifest(Request $request): Response
    {
        try {
            $video = $this->authService->authVideo($request, JwtUsage::USAGE_PUBLIC_VIDEO_ACCESS);
            $manifestContent = $this->manifestService->getManifest($video);
            $sessionId = $this->sessionService->generate($request);

            $this->shareService->addView($video, $sessionId);

            return new Response($manifestContent, 200, ['Content-Type' => MimeType::APPLICATION_MPEGURL->value]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve an PNG thumbnail image from CDN. Works as a proxy to the CDN. Thumbnail is a base64 encoded PNG image.",
        summary: "Retrieve an PNG thumbnail image from CDN",
        tags: [self::TAG],
    )]
    #[ResponseFile(mimeType: MimeType::IMAGE_PNG, description: "Thumbnail image")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/thumbnail', name: 'video_thumbnail', methods: ['GET'])]
    public function getThumbnail(Request $request): Response
    {
        try {
            $video = $this->authService->authVideo($request, JwtUsage::USAGE_VIDEO_ACCESS);
            $thumbnail = $video->getThumbnail();

            if (!$thumbnail) {
                throw new NotFoundException('Thumbnail not found');
            }

            $thumbnailData = base64_decode($thumbnail);
            if (!$thumbnailData) {
                throw new InternalException('Failed to decode thumbnail.');
            }

            return new Response($thumbnailData, 200, [
                'Content-Type' => MimeType::IMAGE_PNG->value,
                'Content-Disposition' => 'inline; filename="thumbnail.png"',
            ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
