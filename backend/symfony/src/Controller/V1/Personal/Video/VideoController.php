<?php

namespace App\Controller\V1\Personal\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\SearchQueryRequest;
use App\DTO\Video\UploadRequest;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Helper\Video\ThirdParty;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StorageService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
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
     * @var StorageService $storageService
     */
    private StorageService $storageService;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @param BaseControllerLocator $locator
     * @param JwtService $jwtService
     * @param CdnService $cdnService
     * @param VideoService $videoService
     * @param FolderService $folderService
     * @param StorageService $storageService
     * @param ShareService $shareService
     */
    public function __construct(
        BaseControllerLocator $locator,
        JwtService $jwtService,
        CdnService $cdnService,
        VideoService $videoService,
        FolderService $folderService,
        StorageService $storageService,
        ShareService $shareService
    )
    {
        parent::__construct($locator);
        $this->jwtService = $jwtService;
        $this->cdnService = $cdnService;
        $this->videoService = $videoService;
        $this->folderService = $folderService;
        $this->storageService = $storageService;
        $this->shareService = $shareService;
    }


    #[Route('/upload', name: 'user_video_upload', methods: ['POST'])]
    public function createUploadLink(Request $request, UploadRequest $uploadRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $this->storageService->checkStorageBeforeUpload($account->getStorage(), $uploadRequest->size);

            $videoToken = $this->jwtService->generateToken($account, JwtUsage::USAGE_UPLOAD, [
                'name' => $uploadRequest->name,
                'folder' => $uploadRequest->folderId,
            ]);

            $data = $this->cdnService->createUploadData([
                'size' => $uploadRequest->size,
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
            $folderId = $videoQueryRequest->getFolderId();

            $folderData = $this->folderService->getAccountFolderDataById($account, $folderId);

            $videos = $this->videoService->getVideos(
                $account,
                $folderData,
                $videoQueryRequest
            );

            $this->videoService->addThumbnailToVideos($videos->getData(), $account);

            return $this->re->withData($videos, [Video::VIDEOS_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/search', name: 'user_videos_search', methods: ['GET'])]
    public function searchVideos(Request $request, SearchQueryRequest $searchQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videos = $this->videoService->searchVideos(
                $account,
                $searchQueryRequest->phrase,
                $searchQueryRequest
            );

            $this->videoService->addThumbnailToVideos($videos->getData(), $account);

            return $this->re->withData($videos, [Video::VIDEOS_READ]);
        } catch (InternalException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{hash}', name: 'user_video_detail', methods: ['GET'])]
    public function getVideoDetail(Request $request, string $hash): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoByHash($account, $hash);

            $this->videoService->addVideoUrlToVideo($video, $account);

            return $this->re->withData($video, [Video::VIDEO_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID . '/recommend', name: 'user_video_recommendation', methods: ['GET'])]
    public function getVideoRecommendation(Request $request, int $id, VideoQueryRequest $videoQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $videos = $this->videoService->getVideoRecommendations(
                $video,
                $videoQueryRequest
            );

            $this->videoService->addThumbnailToVideos($videos->getData(), $account);

            return $this->re->withData($videos, [Video::VIDEOS_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID . '/download', name: 'user_video_download', methods: ['GET'])]
    public function downloadVideo(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $link = $this->cdnService->getDownloadLink($video);
            return $this->re->withData(['downloadLink' => $link]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID . '/share', name: 'user_video_shares', methods: ['GET'])]
    public function getVideoSharedUsers(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $shares = $video->getShares();

            return $this->re->withData($shares, [ShareVideo::SHARE_VIDEO_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID . '/share/public', name: 'user_video_shares_public', methods: ['GET'])]
    public function getVideoSharesPublic(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $shares = $video->getSharesPublic();

            return $this->re->withData([
                'maxViews' => $this->shareService->getPublicLinkViewLimit(),
                'shares' => $this->serialize($shares, [ShareVideoPublic::VIDEO_SHARED_PUBLIC_READ])
            ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, 'user_update_video', methods: ['PUT'])]
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

    #[Route(RegexRoute::ID, name: 'user_delete_video', methods: ['DELETE'])]
    public function deleteVideo(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);
            $this->videoService->deleteVideos([$video], [ThirdParty::CDN]);
            return $this->re->withMessage('Video deleted.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
