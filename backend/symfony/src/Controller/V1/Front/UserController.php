<?php

namespace App\Controller\V1\Front;

use App\Controller\ApiController;
use App\DTO\User\LoginRequest;
use App\DTO\User\RegisterRequest;
use App\Helper\Api\Exception\BadGatewayException;
use App\Helper\Api\Exception\BadRequestException;
use App\Helper\Api\Exception\ConflictException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Api\Exception\NotFoundException;
use App\Helper\Api\ResponseEntity;
use App\Helper\Jwt\JwtUsage;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use App\Service\Account\AccountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends ApiController
{
    /**
     * @var AccountService $userService
     */
    private AccountService $userService;

    /**
     * @var MailerService $mailerService
     */
    private MailerService $mailerService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @param ResponseEntity $re
     * @param EntityManagerInterface $em
     * @param AccountService $userService
     * @param MailerService $mailerService
     * @param JwtService $jwtService
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResponseEntity         $re,
        EntityManagerInterface $em,
        SerializerInterface    $serializer,
        AccountService         $userService,
        MailerService          $mailerService,
        JwtService             $jwtService,
    ) {
        parent::__construct($re, $em, $serializer);
        $this->userService = $userService;
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
            $user = $this->userService->registerUser($registerRequest->email, $registerRequest->password);

            $this->mailerService->sendActivationEmail(
                $user->getEmail(),
                $this->jwtService->generateToken($user, JwtUsage::USAGE_ACCOUNT_ACTIVATION)
            );

            return $this->re->withMessage('User registered successfully.', Response::HTTP_CREATED);
        } catch (ConflictException|InternalException|NotFoundException|BadGatewayException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(LoginRequest $loginRequest, Request $request): JsonResponse
    {
        try {
            $user = $this->userService->loginUser($loginRequest->email, $loginRequest->password);

            $accessToken = $this->jwtService->generateToken($user, JwtUsage::USAGE_API_ACCESS);
            $refreshToken = $this->jwtService->createOrUpdateRefreshToken($user, $request)->getRefreshToken();

            return $this->re->withData([
                'tokens' => [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken
                ],
                'user' => $this->serialize($user)
            ]);

        } catch (BadRequestException $e) {
            return $this->re->withException($e);
        }
    }
}
