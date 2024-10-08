<?php

namespace App\Controller\V1\Admin\Video;

use App\Attribute\OpenApi\Request\Query\QueryFilter;
use App\Attribute\OpenApi\Request\Query\QueryFilterProperty;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyEmail;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyIds;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\VideoFilterRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Regex\RegexRoute;
use App\Helper\Video\FolderData;
use App\Service\Video\VideoService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/videos')]
class VideoController extends BasePersonalController
{
    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    public const TAG = 'admin/videos';

    /**
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    #[OA\Get(
        description: "Retrieve a list of videos.",
        summary: "Get videos",
        tags: [self::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::NAME, SortBy::EMAIL, SortBy::CREATE_DATE, SortBy::SIZE, SortBy::LENGTH])]
    #[QueryOrderBy]
    #[QueryFilter(
        properties: [
            new QueryFilterProperty(name: 'name', description: 'Video name', example: 'example_video'),
            new QueryFilterPropertyEmail(),
            new QueryFilterProperty( name: 'md5', description: 'Video MD5 hash', example: 'd41d8cd98f00b204e9800998ecf8427e'),
            new QueryFilterProperty(name: 'hash', description: 'Video unique hash', example: 'abc123xyz'),
            new QueryFilterProperty(name: 'cdnId', description: 'Video CDN ID', example: 'cdn_456def')
        ]
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], pagination: true, description: "List of videos")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_videos', methods: ['GET'])]
    public function getVideos(PaginatorRequest $paginatorRequest, ?VideoFilterRequest $filterRequest): JsonResponse
    {
        $folderData = new FolderData(null, false);
        $accounts = $this->videoService->getVideos(null, $folderData, $paginatorRequest, $filterRequest);
        return $this->re->withData($accounts, [Video::VIDEOS_READ]);
    }

    #[OA\Delete(
        description: "Batch delete videos by their ids.",
        summary: "Delete videos",
        tags: [self::TAG],
    )]
    #[QueryFilter(properties: [new QueryFilterPropertyIds()])]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], description: "Deleted videos")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_videos_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $videos = $this->videoService->getVideosByIds($filter->ids);

            foreach ($videos as $video) {
                $this->videoService->deleteVideo($video);
            }

            return $this->re->withData($videos, [Video::VIDEOS_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a video by id.",
        summary: "Get video",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEO_READ], collection: false, description: "Video detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_video', methods: ['GET'])]
    public function getVideoDetail(int $id): JsonResponse
    {
        try {
            $video = $this->videoService->getVideoById($id);
            return $this->re->withData($video, [Video::VIDEO_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Updates video by id.",
        summary: "Update video",
        requestBody: new RequestBody(entityClass: VideoRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEO_READ], collection: false, description: "Updated video")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_video_update', methods: ['PUT'])]
    public function updateVideo(int $id, VideoRequest $videoRequest): JsonResponse
    {
        try {
            $video = $this->videoService->getVideoById($id);
            $this->videoService->updateVideo($video, $videoRequest->name, $video->getFolder());
            return $this->re->withData($video, [Video::VIDEO_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete video by ids.",
        summary: "Delete video",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Video deleted successfully")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_video_delete', methods: ['DELETE'])]
    public function deleteVideo(int $id): JsonResponse
    {
        try {
            $video = $this->videoService->getVideoById($id);
            $this->videoService->deleteVideo($video);
            return $this->re->withMessage('Video deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
