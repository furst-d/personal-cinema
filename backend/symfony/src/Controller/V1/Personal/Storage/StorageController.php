<?php

namespace App\Controller\V1\Personal\Storage;

use App\Controller\V1\Personal\BasePersonalController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage')]
class StorageController extends BasePersonalController
{
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
