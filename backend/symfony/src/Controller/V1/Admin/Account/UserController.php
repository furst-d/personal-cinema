<?php

namespace App\Controller\V1\Admin\Account;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\AbstractQueryRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Video\VideoRequest;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Locator\BaseControllerLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class UserController extends BasePersonalController
{
    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @param BaseControllerLocator $locator
     * @param AccountService $accountService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AccountService $accountService
    )
    {
        parent::__construct($locator);
        $this->accountService = $accountService;
    }

    #[Route('', name: 'admin_users', methods: ['GET'])]
    public function getUsers(PaginatorRequest $paginatorRequest): JsonResponse
    {
        $accounts = $this->accountService->getAccounts($paginatorRequest->getLimit(), $paginatorRequest->getOffset());
        return $this->re->withData($accounts, ['account:read']);
    }
}
