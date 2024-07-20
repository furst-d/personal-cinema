<?php

namespace App\Controller\V1\Admin\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\PaginatorRequest;
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
        $accounts = $this->videoService->getVideos(null, null, $paginatorRequest->getLimit(), $paginatorRequest->getOffset());
        return $this->re->withData($accounts, ['video:read']);
    }
}
