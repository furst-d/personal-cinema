<?php

namespace App\Controller\V1\Admin\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\VideoFilterRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Helper\Video\FolderData;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/videos')]
class VideoController extends BasePersonalController
{
    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @param BaseControllerLocator $locator
     * @param VideoService $videoService
     */
    public function __construct(
        BaseControllerLocator $locator,
        VideoService $videoService
    )
    {
        parent::__construct($locator);
        $this->videoService = $videoService;
    }

    #[Route('', name: 'admin_videos', methods: ['GET'])]
    public function getVideos(PaginatorRequest $paginatorRequest, ?VideoFilterRequest $filterRequest): JsonResponse
    {
        $folderData = new FolderData(null, false);
        $accounts = $this->videoService->getVideos(null, $folderData, $paginatorRequest, $filterRequest);
        return $this->re->withData($accounts, [Video::VIDEOS_READ]);
    }

    #[Route('', name: 'admin_users_batch_delete', methods: ['DELETE'])]
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

    #[Route('/{id<\d+>}', name: 'admin_video', methods: ['GET'])]
    public function getVideoDetail(int $id): JsonResponse
    {
        try {
            $video = $this->videoService->getVideoById($id);
            return $this->re->withData($video, [Video::VIDEO_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/{id<\d+>}', name: 'admin_video_update', methods: ['PUT'])]
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

    #[Route('/{id<\d+>}', name: 'admin_video_delete', methods: ['DELETE'])]
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
