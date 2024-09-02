<?php

namespace App\Controller\V1\Personal\Account;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Account\AccountStatsResponse;
use App\DTO\Account\DeleteAccountRequest;
use App\DTO\Account\PasswordChangeRequest;
use App\Entity\Video\Folder;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Service\Account\AccountService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\FolderService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
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

    public const TAG = 'personal/account';

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

    #[OA\Get(
        description: "Retrieve a user's account statistics.",
        summary: "Get user's account statistics",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: AccountStatsResponse::class, description: "User's account statistics")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/stats', name: 'account_stats', methods: ['GET'])]
    public function getStats(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        return $this->re->withData($this->accountService->getStats($account));
    }

    #[OA\Post(
        description: "Change user's password.",
        summary: "Change user's password",
        requestBody: new RequestBody(entityClass: PasswordChangeRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Password changed successfully.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Delete(
        description: "Delete user's account.",
        summary: "Delete user's account",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Account deleted successfully.")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new ForbiddenException(AccountService::INVALID_PASSWORD_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'account_delete', methods: ['DELETE'])]
    public function deleteAccount(Request $request, DeleteAccountRequest $deleteAccountRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $this->accountService->checkPasswordAndDeleteAccount($account, $deleteAccountRequest->password);
            return $this->re->withMessage("Account deleted successfully.");
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
