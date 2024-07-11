<?php

namespace App\Controller\V1\Private\Folder;

use App\Controller\V1\Private\BasePrivateController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/folders')]
class FolderController extends BasePrivateController
{
    #[Route('', name: 'user_folders')]
    public function index(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        return $this->re->withData($account->getFolders());
    }
}
