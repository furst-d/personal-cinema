<?php

namespace App\Controller\V1\Private;

use App\Controller\ApiController;
use App\Service\Auth\AuthService;
use App\Service\Locator\BaseControllerLocator;

class BasePrivateController extends ApiController
{
    /**
     * @var BaseControllerLocator $locator
     */
    protected BaseControllerLocator $locator;

    /**
     * @var AuthService $authService
     */
    protected AuthService $authService;

    /**
     * @param BaseControllerLocator $locator
     * @param AuthService $authService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AuthService $authService
    )
    {
        parent::__construct($locator);
        $this->authService = $authService;
    }
}
