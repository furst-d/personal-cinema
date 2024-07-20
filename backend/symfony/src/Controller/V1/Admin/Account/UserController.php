<?php

namespace App\Controller\V1\Admin\Account;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\AbstractQueryRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Video\VideoRequest;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Locator\BaseControllerLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class UserController extends BasePersonalController
{
    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @param BaseControllerLocator $locator
     * @param AccountService $accountService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AccountService $accountService
    )
    {
        parent::__construct($locator);
        $this->accountService = $accountService;
    }

    #[Route('', name: 'admin_users', methods: ['GET'])]
    public function getUsers(PaginatorRequest $paginatorRequest): JsonResponse
    {
        $accounts = $this->accountService->getAccounts($paginatorRequest->getLimit(), $paginatorRequest->getOffset());
        return $this->re->withData($accounts, ['account:read']);
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

            $backendUrl = $_ENV['BACKEND_URL'];
            return $this->re->withData([
                'video' => $this->serialize($video, ['video:read']),
                'url' => "$backendUrl/v1/private/videos/url?token=$token"
            ]);
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
