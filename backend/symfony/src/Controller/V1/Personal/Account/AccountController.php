<?php

namespace App\Controller\V1\Personal\Account;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Account\PasswordChangeRequest;
use App\DTO\Video\UploadRequest;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoRequest;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Helper\Generator\UrlGenerator;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/account')]
class AccountController extends BasePersonalController
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

    #[Route('/change-password', name: 'account_change_password', methods: ['POST'])]
    public function changePassword(Request $request, PasswordChangeRequest $passwordChangeRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $this->accountService->checkAndChangePassword($account, $passwordChangeRequest->oldPassword, $passwordChangeRequest->newPassword);

            return $this->re->withMessage("Password changed successfully.");
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
