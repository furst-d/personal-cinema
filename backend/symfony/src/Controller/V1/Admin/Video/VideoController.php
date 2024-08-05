<?php

namespace App\Controller\V1\Admin\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\PaginatorRequest;
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
    public function getVideos(PaginatorRequest $paginatorRequest): JsonResponse
    {
        $folderData = new FolderData(null, false);
        $accounts = $this->videoService->getVideos(null, $folderData, $paginatorRequest);
        return $this->re->withData($accounts, ['video:read']);
    }
}
