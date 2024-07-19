<?php

namespace App\Controller\V1\Personal;

use App\Controller\ApiController;
use App\Entity\Account\Account;
use Symfony\Component\HttpFoundation\Request;

class BasePersonalController extends ApiController
{
    /**
     * @param Request $request
     * @return Account
     */
    protected function getAccount(Request $request): Account
    {
        return $request->attributes->get('account');
    }
}
