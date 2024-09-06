<?php

namespace App\Controller\V1\Private;

use App\Controller\ApiController;
use App\Service\Auth\AuthService;
use Symfony\Contracts\Service\Attribute\Required;

class BasePrivateController extends ApiController
{
    /**
     * @var AuthService $authService
     */
    protected AuthService $authService;

    /**
     * @param AuthService $authService
     */
    #[Required]
    public function setAuthService(AuthService $authService): void
    {
        $this->authService = $authService;
    }
}
