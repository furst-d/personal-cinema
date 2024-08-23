<?php

namespace App\Controller\V1\Private\Video;

use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\QualityRequest;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\ManifestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/videos')]
class VideoController extends BasePrivateController
{
    /**
     * @var ManifestService $manifestService
     */
    private ManifestService $manifestService;

    /**
     * @param BaseControllerLocator $locator
     * @param AuthService $authService
     * @param ManifestService $manifestService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AuthService $authService,
        ManifestService $manifestService
    )
    {
        parent::__construct($locator, $authService);
        $this->manifestService = $manifestService;
    }

    #[Route('/url', name: 'video_manifest', methods: ['GET'])]
    public function getManifest(Request $request, QualityRequest $qualityRequest): Response
    {
        try {
            $video = $this->authService->authVideo($request, JwtUsage::USAGE_VIDEO_ACCESS);
            $manifestContent = $this->manifestService->getManifest($video, $qualityRequest->quality);

            return new Response($manifestContent, 200, ['Content-Type' => 'application/vnd.apple.mpegurl']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

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
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="thumbnail.png"',
            ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
