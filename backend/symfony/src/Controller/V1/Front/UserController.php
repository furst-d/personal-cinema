<?php

namespace App\Controller\V1\Front;

use App\Controller\ApiController;
use App\DTO\Account\LoginRequest;
use App\DTO\Account\RegisterRequest;
use App\DTO\Account\TokenRequest;
use App\Exception\ApiException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
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

    /**
     * @param BaseControllerLocator $locator
     * @param AccountService $accountService
     * @param MailerService $mailerService
     * @param JwtService $jwtService
     */
    public function __construct(
        BaseControllerLocator    $locator,
        AccountService         $accountService,
        MailerService          $mailerService,
        JwtService             $jwtService,
    ) {
        parent::__construct($locator);
        $this->accountService = $accountService;
        $this->mailerService = $mailerService;
        $this->jwtService = $jwtService;
    }

    /**
     * @param RegisterRequest $registerRequest
     * @return JsonResponse
     */
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

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(LoginRequest $loginRequest, Request $request): JsonResponse
    {
        try {
            $user = $this->accountService->loginUser($loginRequest->email, $loginRequest->password);

            $accessToken = $this->jwtService->generateToken($user, JwtUsage::USAGE_API_ACCESS);
            $refreshToken = $this->jwtService->createOrUpdateRefreshToken($user, $request)->getRefreshToken();

            return $this->re->withData([
                'tokens' => [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken
                ],
                'user' => $this->serialize($user)
            ]);

        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/refresh-token', name: 'user_refresh_token', methods: ['POST'])]
    public function refreshToken(TokenRequest $tokenRequest): JsonResponse
    {
        try {
            return $this->re->withData([
                'tokens' => [
                    'access_token' =>  $this->jwtService->refreshToken($tokenRequest->token)
                ],
            ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
