<?php

namespace App\Controller\V1\Personal\Folder;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\FolderQueryRequest;
use App\DTO\Video\FolderRequest;
use App\DTO\Video\SearchQueryRequest;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareFolder;
use App\Exception\ApiException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/folders')]
class FolderController extends BasePersonalController
{
    /**
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    /**
     * @param BaseControllerLocator $locator
     * @param FolderService $folderService
     */
    public function __construct(
        BaseControllerLocator $locator,
        FolderService $folderService,
    )
    {
        parent::__construct($locator);
        $this->folderService = $folderService;
    }

    #[Route('', name: 'user_folders', methods: ['GET'])]
    public function getFolders(Request $request, FolderQueryRequest $folderQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $parentFolderId = $folderQueryRequest->getParentId();

            $folderData = $this->folderService->getAccountFolderDataById($account, $parentFolderId);

            $folders = $this->folderService->getFolders(
                $account,
                $folderData,
                $folderQueryRequest
            );

            return $this->re->withData($folders, [Folder::FOLDER_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/search', name: 'user_folders_search', methods: ['GET'])]
    public function searchFolders(Request $request, SearchQueryRequest $searchQueryRequest): JsonResponse
    {
        $account = $this->getAccount($request);

        $videos = $this->folderService->searchFolders(
            $account,
            $searchQueryRequest->phrase,
            $searchQueryRequest
        );

        return $this->re->withData($videos, [Folder::FOLDER_READ]);
    }

    #[Route(RegexRoute::ID . '/share', name: 'user_folder_shares', methods: ['GET'])]
    public function getVideoSharedUsers(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $folder = $this->folderService->getAccountFolderById($account, $id);

            $shares = $folder->getShares();

            return $this->re->withData($shares, [ShareFolder::SHARE_FOLDER_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('', name: 'user_create_folder', methods: ['POST'])]
    public function createFolder(Request $request, FolderRequest $folderRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $folder = $this->folderService->createFolder($account, $folderRequest->name, $folderRequest->parentId);
            return $this->re->withData($folder, [Folder::FOLDER_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, 'user_update_folder', methods: ['PUT'])]
    public function updateFolder(Request $request, FolderRequest $folderRequest, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $folder = $this->folderService->getAccountFolderById($account, $id);
            $this->folderService->updateFolder($account, $folder, $folderRequest->name, $folderRequest->parentId);
            return $this->re->withMessage('Folder updated.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'user_delete_folder', methods: ['DELETE'])]
    public function deleteFolder(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $folder = $this->folderService->getAccountFolderById($account, $id);
            $this->folderService->deleteFolder($folder);
            return $this->re->withMessage('Folder deleted.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
