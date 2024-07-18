<?php

namespace App\Controller\V1\Private\Video;

use App\Controller\V1\Private\BasePrivateController;
use App\Exception\ApiException;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Locator\BaseControllerLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/videos')]
class VideoController extends BasePrivateController
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

    #[Route('/url', name: 'video_manifest', methods: ['GET'])]
    public function getManifest(Request $request): Response
    {
        try {
            $video = $this->authService->authVideo($request);
            $manifestContent = $this->cdnService->getManifest($video);

            return new Response($manifestContent, 200, ['Content-Type' => 'application/vnd.apple.mpegurl']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
