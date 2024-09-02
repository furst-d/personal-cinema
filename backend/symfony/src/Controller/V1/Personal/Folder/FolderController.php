<?php

namespace App\Controller\V1\Personal\Folder;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\FolderQueryRequest;
use App\DTO\Video\FolderRequest;
use App\DTO\Video\SearchQueryRequest;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareFolder;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/folders')]
class FolderController extends BasePersonalController
{
    /**
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    public const TAG = 'personal/folders';

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

    #[OA\Get(
        description: "Retrieve a list of folders.",
        summary: "Get user's folders",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Folder::class, groups: [Folder::FOLDER_READ], pagination: true, description: "List of folders")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_folders', methods: ['GET'])]
    public function getFolders(Request $request, #[MapQueryString] FolderQueryRequest $folderQueryRequest): JsonResponse
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

    #[OA\Get(
        description: "Retrieve a searched list of folders.",
        summary: "Get user's searched folders",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Folder::class, groups: [Folder::FOLDER_READ], pagination: true, description: "List of searched folders")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/search', name: 'user_folders_search', methods: ['GET'])]
    public function searchFolders(Request $request, #[MapQueryString] SearchQueryRequest $searchQueryRequest): JsonResponse
    {
        $account = $this->getAccount($request);

        $videos = $this->folderService->searchFolders(
            $account,
            $searchQueryRequest->phrase,
            $searchQueryRequest
        );

        return $this->re->withData($videos, [Folder::FOLDER_READ]);
    }

    #[OA\Get(
        description: "Retrieves a users with permission to access the folder.",
        summary: "Get user's shared users for a folder",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: ShareFolder::class, groups: [ShareFolder::SHARE_FOLDER_READ], description: "List of folder's shared users")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Post(
        description: "Create a folder.",
        summary: "Create a folder",
        requestBody: new RequestBody(entityClass: FolderRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Folder::class, groups: [Folder::FOLDER_READ], description: "Created folder")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Put(
        description: "Update a folder.",
        summary: "Update user's folder",
        requestBody: new RequestBody(entityClass: FolderRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Folder updated.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Delete(
        description: "Delete a folder.",
        summary: "Delete user's folder",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Folder deleted.")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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
