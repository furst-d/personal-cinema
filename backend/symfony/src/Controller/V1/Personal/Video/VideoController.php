<?php

namespace App\Controller\V1\Personal\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\UploadRequest;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/videos')]
class VideoController extends BasePersonalController
{
    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @param BaseControllerLocator $locator
     * @param JwtService $jwtService
     * @param CdnService $cdnService
     * @param VideoService $videoService
     */
    public function __construct(
        BaseControllerLocator $locator,
        JwtService $jwtService,
        CdnService $cdnService,
        VideoService $videoService
    )
    {
        parent::__construct($locator);
        $this->jwtService = $jwtService;
        $this->cdnService = $cdnService;
        $this->videoService = $videoService;
    }


    #[Route('/upload', name: 'user_video_upload', methods: ['POST'])]
    public function createUploadLink(Request $request, UploadRequest $uploadRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videoToken = $this->jwtService->generateToken($account, JwtUsage::USAGE_UPLOAD, [
                'name' => $uploadRequest->name,
                'folder' => $uploadRequest->folderId,
            ]);

            $data = $this->cdnService->createUploadData([
                'params' => json_encode([
                    'video_token' => $videoToken,
                ])
            ]);

            return $this->re->withData($data);
        } catch (InternalException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{videoId<\d+>}', name: 'user_video_detail', methods: ['GET'])]
    public function getVideoDetail(Request $request, int $videoId): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $videoId);

            $token = $this->jwtService->generateToken($account, JwtUsage::USAGE_VIDEO_ACCESS, [
                'video_id' => $video->getId(),
            ]);

            $videoData = $video->toArray();
            $backendUrl = $_ENV['BACKEND_URL'];
            $videoData['url'] = "$backendUrl/v1/private/videos/url?token=$token";

            return $this->re->withData($videoData);
        } catch (NotFoundException|InternalException $e) {
            return $this->re->withException($e);
        }
    }
}
