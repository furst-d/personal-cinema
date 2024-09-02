<?php

namespace App\Controller\V1\Front;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\ApiController;
use App\DTO\Account\AccessTokenResponse;
use App\DTO\Account\LoginRequest;
use App\DTO\Account\LoginResponse;
use App\DTO\Account\RegisterRequest;
use App\DTO\Account\TokenRefreshResponse;
use App\DTO\Account\TokenRequest;
use App\DTO\Account\TokenResponse;
use App\Entity\Account\Account;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\InternalException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends ApiController
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

    public const TAG = 'public/users';

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
        JwtService $jwtService,
    ) {
        parent::__construct($locator);
        $this->accountService = $accountService;
        $this->mailerService = $mailerService;
        $this->jwtService = $jwtService;
    }

    #[OA\Post(
        description: "Register a new user.",
        summary: "Register user",
        requestBody: new RequestBody(entityClass: RegisterRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "User registered successfully. Please check your email to activate your account.", responseCode: Response::HTTP_CREATED)]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new ConflictException(AccountService::ACCOUNT_ALREADY_EXISTS_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        try {
            $user = $this->accountService->registerUser($registerRequest->email, $registerRequest->password);

            $activationToken = $this->jwtService->generateToken($user, JwtUsage::USAGE_ACCOUNT_ACTIVATION);
            $this->mailerService->sendAccountActivation(
                $user->getEmail(),
                $activationToken
            );

            return $this->re->withMessage(
                'User registered successfully. Please check your email to activate your account.',
                Response::HTTP_CREATED
            );
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Login a user.",
        summary: "Login user",
        requestBody: new RequestBody(entityClass: LoginRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: LoginResponse::class, collection: false, description: "Logged user")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new InternalException())]
    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, LoginRequest $loginRequest): JsonResponse
    {
        try {
            $user = $this->accountService->loginUser($loginRequest->email, $loginRequest->password, ['ROLE_USER']);

            $accessToken = $this->jwtService->generateToken($user, JwtUsage::USAGE_API_ACCESS);
            $refreshToken = $this->jwtService->createOrUpdateRefreshToken($user, $request)->getRefreshToken();

            return $this->re->withData(new LoginResponse(
                new TokenResponse($accessToken, $refreshToken),
                $this->serialize($user, [Account::ACCOUNT_READ])
            ));

        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Login an admin user.",
        summary: "Login admin user",
        requestBody: new RequestBody(entityClass: LoginRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: LoginResponse::class, collection: false, description: "Logged user")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new InternalException())]
    #[Route('/login/admin', name: 'user_admin_login', methods: ['POST'])]
    public function adminLogin(Request $request, LoginRequest $loginRequest): JsonResponse
    {
        try {
            $user = $this->accountService->loginUser($loginRequest->email, $loginRequest->password, ['ROLE_USER', 'ROLE_ADMIN']);

            $accessToken = $this->jwtService->generateToken($user, JwtUsage::USAGE_API_ACCESS);
            $refreshToken = $this->jwtService->createOrUpdateRefreshToken($user, $request)->getRefreshToken();

            return $this->re->withData(new LoginResponse(
                new TokenResponse($accessToken, $refreshToken),
                $this->serialize($user, [Account::ACCOUNT_READ])
            ));

        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Generate a new access token by refresh token.",
        summary: "Generate new access token",
        requestBody: new RequestBody(entityClass: TokenRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: TokenRefreshResponse::class, collection: false, description: "New access token")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new InternalException())]
    #[Route('/refresh-token', name: 'user_refresh_token', methods: ['POST'])]
    public function refreshToken(TokenRequest $tokenRequest): JsonResponse
    {
        try {
            return $this->re->withData(new TokenRefreshResponse(
                new AccessTokenResponse($this->jwtService->refreshToken($tokenRequest->token)),
            ));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
