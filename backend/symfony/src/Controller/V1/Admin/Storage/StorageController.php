<?php

namespace App\Controller\V1\Admin\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Storage\StorageQueryRequest;
use App\DTO\Storage\StorageRequest;
use App\Entity\Storage\Storage;
use App\Exception\ApiException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StorageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/storage')]
class StorageController extends BasePersonalController
{
    /**
     * @var StorageService $storageService
     */
    private StorageService $storageService;

    /**
     * @param BaseControllerLocator $locator
     * @param StorageService $storageService
     */
    public function __construct(
        BaseControllerLocator $locator,
        StorageService $storageService
    )
    {
        parent::__construct($locator);
        $this->storageService = $storageService;
    }

    #[Route('', name: 'admin_storages', methods: ['GET'])]
    public function getStorages(StorageQueryRequest $storageQueryRequest, ?EmailFilterRequest $filterRequest): JsonResponse
    {
        $storages = $this->storageService->getStorages($storageQueryRequest, $filterRequest);
        return $this->re->withData($storages, [Storage::STORAGE_READ]);
    }

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
