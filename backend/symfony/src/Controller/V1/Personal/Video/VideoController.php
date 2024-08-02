<?php

namespace App\Controller\V1\Personal\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\UploadRequest;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Helper\Generator\UrlGenerator;
use App\Helper\Jwt\JwtUsage;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
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
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    /**
     * @var UrlGenerator $urlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @param BaseControllerLocator $locator
     * @param JwtService $jwtService
     * @param CdnService $cdnService
     * @param VideoService $videoService
     * @param FolderService $folderService
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        BaseControllerLocator $locator,
        JwtService $jwtService,
        CdnService $cdnService,
        VideoService $videoService,
        FolderService $folderService,
        UrlGenerator $urlGenerator
    )
    {
        parent::__construct($locator);
        $this->jwtService = $jwtService;
        $this->cdnService = $cdnService;
        $this->videoService = $videoService;
        $this->folderService = $folderService;
        $this->urlGenerator = $urlGenerator;
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
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('', name: 'user_videos', methods: ['GET'])]
    public function getVideos(Request $request, VideoQueryRequest $videoQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $folderId = $videoQueryRequest->folderId;

            $folder = null;
            if ($folderId) {
                $folder = $this->folderService->getAccountFolderById($account, $folderId);
            }

            $videos = $this->videoService->getVideos($account, $folder, $videoQueryRequest->getLimit(), $videoQueryRequest->getOffset());

            /** @var Video $video */
            foreach ($videos->getData() as $video) {
                if ($video->getThumbnail()) {
                    $video->setThumbnailUrl($this->urlGenerator->generateThumbnail($account, $video));
                }
            }

            return $this->re->withData($videos, ['videos:read']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{videoId<\d+>}', name: 'user_video_detail', methods: ['GET'])]
    public function getVideoDetail(Request $request, int $videoId): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $videoId);

            if ($video->getPath()) {
                $video->setVideoUrl($this->urlGenerator->generateVideo($account, $video));
            }

            return $this->re->withData($video, ['video:read']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{id<\d+>}', 'user_update_video', methods: ['PUT'])]
    public function updateVideo(Request $request, VideoRequest $videoRequest, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $folder = null;
            if ($videoRequest->folderId) {
                $folder = $this->folderService->getAccountFolderById($account, $videoRequest->folderId);
            }

            $this->videoService->updateVideo($video, $videoRequest->name, $folder);
            return $this->re->withMessage('Video updated.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{id<\d+>}', name: 'user_delete_video', methods: ['DELETE'])]
    public function deleteVideo(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);
            $this->videoService->deleteVideo($video);
            return $this->re->withMessage('Video deleted.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
