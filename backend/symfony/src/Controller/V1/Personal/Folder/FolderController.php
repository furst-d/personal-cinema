<?php

namespace App\Controller\V1\Personal\Folder;

use App\Controller\V1\Personal\BasePersonalController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/folders')]
class FolderController extends BasePersonalController
{
    #[Route('', name: 'user_folders')]
    public function index(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        return $this->re->withData($account->getFolders());
    }
}
