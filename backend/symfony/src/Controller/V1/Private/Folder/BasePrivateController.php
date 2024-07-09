<?php

namespace App\Controller\V1\Private\Folder;

use App\Controller\ApiController;
use App\Entity\Account\Account;
use Symfony\Component\HttpFoundation\Request;

class BasePrivateController extends ApiController
{
    /**
     * @param Request $request
     * @return Account|null
     */
    protected function getAccount(Request $request): ?Account
    {
        return $request->attributes->get('account');
    }
}
