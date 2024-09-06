<?php

namespace App\Controller\V1\Personal\Video;

use App\Attribute\OpenApi\Request\Query\QueryInt;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\Query\QueryString;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\PublicVideoSharesResponse;
use App\DTO\Video\SearchQueryRequest;
use App\DTO\Video\UploadRequest;
use App\DTO\Video\UploadResponse;
use App\DTO\Video\VideoDownloadLinkResponse;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\FullStorageException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\TooLargeException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Helper\Video\ThirdParty;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Storage\StorageService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
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

    public const TAG = 'personal/videos';

    /**
     * @param JwtService $jwtService
     * @param CdnService $cdnService
     * @param VideoService $videoService
     * @param FolderService $folderService
     * @param StorageService $storageService
     * @param ShareService $shareService
     */
    public function __construct(
        JwtService $jwtService,
        CdnService $cdnService,
        VideoService $videoService,
        FolderService $folderService,
        StorageService $storageService,
        ShareService $shareService
    )
    {
        $this->jwtService = $jwtService;
        $this->cdnService = $cdnService;
        $this->videoService = $videoService;
        $this->folderService = $folderService;
        $this->storageService = $storageService;
        $this->shareService = $shareService;
    }


    #[OA\Post(
        description: "Generates a link that can be used to upload a video file to the CDN server.",
        summary: "Create an upload link for a video",
        requestBody: new RequestBody(entityClass: UploadRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: UploadResponse::class, collection: false, description: "Upload data")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new FullStorageException(StorageService::FILE_EXCEEDS_STORAGE_LIMIT_MESSAGE))]
    #[ResponseError(exception: new TooLargeException(StorageService::FILE_TOO_LARGE_MESSAGE, ['maxFileSize' => "100 MB"]))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieve a list of videos.",
        summary: "Get user's videos",
        tags: [self::TAG],
    )]
    #[QueryInt(name: 'folderId', description: "Folder ID")]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::NAME, SortBy::EMAIL, SortBy::UPDATE_DATE])]
    #[QueryOrderBy]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], pagination: true, description: "List of videos")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieve a searched list of videos.",
        summary: "Get user's searched videos",
        tags: [self::TAG],
    )]
    #[QueryString(name: 'phrase', description: 'Search phrase', required: true)]
    #[QueryLimit]
    #[QueryOffset]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], pagination: true, description: "List of searched videos")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieve a detail of a video.",
        summary: "Get user's video detail",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEO_READ], collection: false, description: "Detail of a video")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieve list of recommended videos.",
        summary: "Get user's video recommendations",
        tags: [self::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::NAME, SortBy::EMAIL, SortBy::UPDATE_DATE])]
    #[QueryOrderBy]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], pagination: true, description: "List of recommended videos")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieves a video download link.",
        summary: "Get user's video download link",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: VideoDownloadLinkResponse::class, collection: false, description: "Download link for the video")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route(RegexRoute::ID . '/download', name: 'user_video_download', methods: ['GET'])]
    public function downloadVideo(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $link = $this->cdnService->getDownloadLink($video);
            return $this->re->withData(new VideoDownloadLinkResponse($link));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieves a users with permission to view the video.",
        summary: "Get user's shared users for a video",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: ShareVideo::class, groups: [ShareVideo::SHARE_VIDEO_READ], description: "List of video's shared users")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieves a user's publicly shared video data.",
        summary: "Get user's publicly shared video data",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: PublicVideoSharesResponse::class, description: "List of user's publicly shared videos")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route(RegexRoute::ID . '/share/public', name: 'user_video_shares_public', methods: ['GET'])]
    public function getVideoSharesPublic(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $video = $this->videoService->getAccountVideoById($account, $id);

            $shares = $video->getSharesPublic();

            return $this->re->withData(new PublicVideoSharesResponse(
                $this->shareService->getPublicLinkViewLimit(),
                $this->serialize($shares, [ShareVideoPublic::VIDEO_SHARED_PUBLIC_READ])
            ));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Update a video.",
        summary: "Update user's video",
        requestBody: new RequestBody(entityClass: VideoRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Video updated.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Delete(
        description: "Delete a video.",
        summary: "Delete user's video",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Video deleted.")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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
