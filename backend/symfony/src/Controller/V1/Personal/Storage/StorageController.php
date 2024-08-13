<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\UploadRequest;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Storage\StorageService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage')]
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

    #[Route('', name: 'user_storage', methods: ['GET'])]
    public function getStorageInfo(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        $storage = $account->getStorage();

        $data = [
            'totalStorage' => $storage->getMaxStorage(),
            'usedStorage' => $storage->getUsedStorage()
        ];

        return $this->re->withData($data);
    }
}
