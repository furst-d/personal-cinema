<?php

namespace App\Controller\V1\Admin\Account;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Admin\Account\AccountQueryRequest;
use App\DTO\Admin\Account\RegisterUserRequest;
use App\DTO\Admin\Account\UpdateUserRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\EmailFilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class UserController extends BasePersonalController
{
    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @var MailerService $mailerService
     */
    private MailerService $mailerService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @param BaseControllerLocator $locator
     * @param AccountService $accountService
     * @param MailerService $mailerService
     * @param JwtService $jwtService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AccountService $accountService,
        MailerService $mailerService,
        JwtService $jwtService
    )
    {
        parent::__construct($locator);
        $this->accountService = $accountService;
        $this->mailerService = $mailerService;
        $this->jwtService = $jwtService;
    }

    #[Route('', name: 'admin_users', methods: ['GET'])]
    public function getUsers(AccountQueryRequest $paginatorRequest, ?EmailFilterRequest $filterRequest): JsonResponse
    {
        $accounts = $this->accountService->getAccounts($paginatorRequest, $filterRequest);
        return $this->re->withData($accounts, [Account::ACCOUNT_READ]);
    }

    #[Route('', name: 'admin_user_register', methods: ['POST'])]
    public function registerUser(RegisterUserRequest $registerUserRequest): JsonResponse
    {
        try {
            $account = $this->accountService->registerUser($registerUserRequest->email, $registerUserRequest->password);
            $this->accountService->updateAccount($account, $registerUserRequest);

            if (!$registerUserRequest->active) {
                $this->mailerService->sendAccountActivation(
                    $account->getEmail(),
                    $this->jwtService->generateToken($account, JwtUsage::USAGE_ACCOUNT_ACTIVATION)
                );
            }

            return $this->re->withData($account, [Account::ACCOUNT_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('', name: 'admin_users_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $accounts = $this->accountService->getAccountsByIds($filter->ids);

            foreach ($accounts as $account) {
                $this->accountService->deleteAccount($account);
            }

            return $this->re->withData($accounts, [Account::ACCOUNT_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/roles', name: 'admin_roles', methods: ['GET'])]
    public function getRoles(): JsonResponse
    {
        return $this->re->withData($this->accountService->getRoles());
    }

    #[Route(RegexRoute::ID, name: 'admin_user', methods: ['GET'])]
    public function getUserDetail(int $id): JsonResponse
    {
        try {
            $account = $this->accountService->getAccountById($id);
            return $this->re->withData($account, [Account::ACCOUNT_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'admin_user_update', methods: ['PUT'])]
    public function updateUser(int $id, UpdateUserRequest $updateUserRequest): JsonResponse
    {
        try {
            $account = $this->accountService->getAccountById($id);
            $this->accountService->updateAccount($account, $updateUserRequest);
            return $this->re->withData($account, [Account::ACCOUNT_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'admin_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $account = $this->accountService->getAccountById($id);
            $this->accountService->deleteAccount($account);
            return $this->re->withMessage('User deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
