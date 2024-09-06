<?php

namespace App\Controller\V1\Front;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\ApiController;
use App\DTO\Account\EmailRequest;
use App\DTO\Account\PasswordResetRequest;
use App\Exception\ApiException;
use App\Exception\BadGatewayException;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/users/password-reset')]
class PasswordResetController extends ApiController
{
    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var MailerService $mailerService
     */
    private MailerService $mailerService;

    /**
     * @param AccountService $accountService
     * @param JwtService $jwtService
     * @param MailerService $mailerService
     */
    public function __construct(
        AccountService $accountService,
        JwtService $jwtService,
        MailerService $mailerService,
    ) {
        $this->accountService = $accountService;
        $this->jwtService = $jwtService;
        $this->mailerService = $mailerService;
    }

    #[OA\Post(
        description: "Change user's password when was used password reset option.",
        summary: "Change password",
        requestBody: new RequestBody(entityClass: PasswordResetRequest::class),
        tags: [UserController::TAG],
    )]
    #[ResponseMessage(message: "Password was successfully changed.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException(JwtService::INVALID_TOKEN_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(AccountService::ACCOUNT_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Route('', name: 'password_reset', methods: ['POST'])]
    public function reset(PasswordResetRequest $passwordResetRequest): JsonResponse
    {
        try {
            $decodedToken = $this->jwtService->decodeToken($passwordResetRequest->token, JwtUsage::USAGE_PASSWORD_RESET);
            $account = $this->accountService->getAccountById($decodedToken['user_id']);
            $this->accountService->changePassword($account, $passwordResetRequest->password);

            return $this->re->withMessage('Password was successfully changed.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Resend email with password reset token.",
        summary: "Resend password reset email",
        requestBody: new RequestBody(entityClass: EmailRequest::class),
        tags: [UserController::TAG],
    )]
    #[ResponseMessage(message: "Email for password change was sent successfully.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new NotFoundException(AccountService::ACCOUNT_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[ResponseError(exception: new BadGatewayException())]
    #[Route('/send', name: 'password_reset_send', methods: ['POST'])]
    public function resendToken(EmailRequest $emailRequest): JsonResponse
    {
        try {
            $user = $this->accountService->getAccountByEmail($emailRequest->email);

            $this->mailerService->sendPasswordReset(
                $user->getEmail(),
                $this->jwtService->generateToken($user, JwtUsage::USAGE_PASSWORD_RESET)
            );

            return $this->re->withMessage('Email for password change was sent successfully.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
