<?php

namespace App\Controller\V1\Admin\Storage;

use App\Attribute\OpenApi\Request\Query\QueryFilter;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyEmail;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Storage\StorageQueryRequest;
use App\DTO\Storage\StorageRequest;
use App\Entity\Storage\Storage;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Regex\RegexRoute;
use App\Service\Storage\StorageService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/storage')]
class StorageController extends BasePersonalController
{
    /**
     * @var StorageService $storageService
     */
    private StorageService $storageService;

    public const TAG = 'admin/storage';

    /**
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    #[OA\Get(
        description: "Retrieve a list of storage sizes.",
        summary: "Get storage sizes",
        tags: [self::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::EMAIL, SortBy::MAX_STORAGE, SortBy::USED_STORAGE, SortBy::FILL_SIZE])]
    #[QueryOrderBy]
    #[QueryFilter(properties: [new QueryFilterPropertyEmail()])]
    #[ResponseData(entityClass: StorageService::class, groups: [Storage::STORAGE_READ], pagination: true, description: "List of storage sizes")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_storages', methods: ['GET'])]
    public function getStorages(StorageQueryRequest $storageQueryRequest, ?EmailFilterRequest $filterRequest): JsonResponse
    {
        $storages = $this->storageService->getStorages($storageQueryRequest, $filterRequest);
        return $this->re->withData($storages, [Storage::STORAGE_READ]);
    }

    #[OA\Get(
        description: "Retrieve a storage size by id.",
        summary: "Get storage size",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Storage::class, groups: [Storage::STORAGE_READ], collection: false, description: "Storage size detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StorageService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_storage', methods: ['GET'])]
    public function getStorageDetail(int $id): JsonResponse
    {
        try {
            $storage = $this->storageService->getStorageById($id);
            return $this->re->withData($storage, [Storage::STORAGE_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Updates storage size by id.",
        summary: "Update storage size",
        requestBody: new RequestBody(entityClass: StorageRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Storage::class, groups: [Storage::STORAGE_READ], collection: false, description: "Updated storage size")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(StorageService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_storage_update', methods: ['PUT'])]
    public function updateStorage(int $id, StorageRequest $storageRequest): JsonResponse
    {
        try {
            $storage = $this->storageService->getStorageById($id);
            $this->storageService->updateMaxStorage($storage, $storageRequest->maxStorage);
            return $this->re->withData($storage, [Storage::STORAGE_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
