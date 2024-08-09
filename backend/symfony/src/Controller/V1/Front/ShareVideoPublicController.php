<?php

namespace App\Controller\V1\Front;

use App\Controller\ApiController;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\SessionService;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @var AuthService $authService
     */
    private AuthService $authService;

    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @param BaseControllerLocator $locator
     * @param VideoService $videoService
     * @param ShareService $shareService
     * @param SessionService $sessionService
     * @param AuthService $authService
     * @param CdnService $cdnService
     */
    public function __construct(
        BaseControllerLocator $locator,
        VideoService $videoService,
        ShareService $shareService,
        SessionService $sessionService,
        AuthService $authService,
        CdnService $cdnService
    )
    {
        parent::__construct($locator);
        $this->videoService = $videoService;
        $this->shareService = $shareService;
        $this->sessionService = $sessionService;
        $this->authService = $authService;
        $this->cdnService = $cdnService;
    }

    #[Route('/url', name: 'public_video_manifest', methods: ['GET'])]
    public function getManifest(Request $request): Response
    {
        try {
            $video = $this->authService->authVideo($request, JwtUsage::USAGE_PUBLIC_VIDEO_ACCESS);
            $manifestContent = $this->cdnService->getManifest($video);
            $sessionId = $this->sessionService->generate($request);

            $this->shareService->addView($video, $sessionId);

            return new Response($manifestContent, 200, ['Content-Type' => 'application/vnd.apple.mpegurl']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{hash}', name: 'public_shared_video_detail', methods: ['GET'])]
    public function getVideoDetail(Request $request, string $hash): JsonResponse
    {
        try {
            $sessionId = $this->sessionService->generate($request);

            $videoShare = $this->shareService->getPublicVideoByHash($hash, $sessionId);
            $video = $videoShare->getVideo();
            $this->videoService->addPublicVideoUrlToVideo($video);

            return $this->re->withData($video, ['video:public:read']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
