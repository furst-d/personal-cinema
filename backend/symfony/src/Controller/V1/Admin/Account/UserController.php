<?php

namespace App\Controller\V1\Admin\Account;

use App\Attribute\OpenApi\Request\Query\QueryFilter;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyEmail;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyIds;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Admin\Account\AccountQueryRequest;
use App\DTO\Admin\Account\RegisterUserRequest;
use App\DTO\Admin\Account\UpdateUserRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\DTO\Filter\EmailFilterRequest;
use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

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

    public const TAG = 'admin/users';

    /**
     * @param AccountService $accountService
     * @param MailerService $mailerService
     * @param JwtService $jwtService
     */
    public function __construct(
        AccountService $accountService,
        MailerService $mailerService,
        JwtService $jwtService
    )
    {
        $this->accountService = $accountService;
        $this->mailerService = $mailerService;
        $this->jwtService = $jwtService;
    }

    #[OA\Get(
        description: "Retrieve a list of users.",
        summary: "Get users",
        tags: [self::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::EMAIL, SortBy::CREATE_DATE, SortBy::IS_ACTIVE])]
    #[QueryOrderBy]
    #[QueryFilter(properties: [new QueryFilterPropertyEmail()])]
    #[ResponseData(entityClass: Account::class, groups: [Account::ACCOUNT_READ], pagination: true, description: "List of users")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_users', methods: ['GET'])]
    public function getUsers(AccountQueryRequest $paginatorRequest, ?EmailFilterRequest $filterRequest): JsonResponse
    {
        $accounts = $this->accountService->getAccounts($paginatorRequest, $filterRequest);
        return $this->re->withData($accounts, [Account::ACCOUNT_READ]);
    }

    #[OA\Post(
        description: "Register a new user.",
        summary: "Register user",
        requestBody: new RequestBody(entityClass: RegisterUserRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Account::class, groups: [Account::ACCOUNT_READ], collection: false, description: "Registered user")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new ConflictException(AccountService::ACCOUNT_ALREADY_EXISTS_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
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

    #[OA\Delete(
        description: "Batch delete users by their ids.",
        summary: "Delete users",
        tags: [self::TAG],
    )]
    #[QueryFilter(properties: [new QueryFilterPropertyIds()])]
    #[ResponseData(entityClass: Account::class, groups: [Account::ACCOUNT_READ], description: "Deleted users")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(AccountService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
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

    #[OA\Get(
        description: "Retrieve a list of roles.",
        summary: "Get roles",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Role::class, description: "List of roles")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('/roles', name: 'admin_roles', methods: ['GET'])]
    public function getRoles(): JsonResponse
    {
        return $this->re->withData($this->accountService->getRoles());
    }

    #[OA\Get(
        description: "Retrieve a user by id.",
        summary: "Get user",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Account::class, groups: [Account::ACCOUNT_READ], collection: false, description: "User detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(AccountService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
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

    #[OA\Put(
        description: "Updates user by id.",
        summary: "Update user",
        requestBody: new RequestBody(entityClass: UpdateUserRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Account::class, groups: [Account::ACCOUNT_READ], collection: false, description: "Updated user")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(AccountService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
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

    #[OA\Delete(
        description: "Delete user by ids.",
        summary: "Delete user",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "User deleted successfully")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(AccountService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
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
