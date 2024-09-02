<?php

namespace App\Controller\V1\Front;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\ApiController;
use App\DTO\Account\EmailRequest;
use App\DTO\Account\TokenRequest;
use App\Exception\ApiException;
use App\Exception\BadGatewayException;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/users/activate')]
class UserActivationController extends ApiController
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
     * @param BaseControllerLocator $locator
     * @param AccountService $accountService
     * @param JwtService $jwtService
     * @param MailerService $mailerService
     */
    public function __construct(
        BaseControllerLocator $locator,
        AccountService $accountService,
        JwtService $jwtService,
        MailerService $mailerService,
    ) {
        parent::__construct($locator);
        $this->accountService = $accountService;
        $this->jwtService = $jwtService;
        $this->mailerService = $mailerService;
    }

    #[OA\Post(
        description: "Activate user account.",
        summary: "Activate account",
        requestBody: new RequestBody(entityClass: TokenRequest::class),
        tags: [UserController::TAG],
    )]
    #[ResponseMessage(message: "User was activated successfully.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException(JwtService::INVALID_TOKEN_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(AccountService::ACCOUNT_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Route('', name: 'user_activate', methods: ['POST'])]
    public function activate(TokenRequest $tokenRequest): JsonResponse
    {
        try {
            $decodedToken = $this->jwtService->decodeToken($tokenRequest->token, JwtUsage::USAGE_ACCOUNT_ACTIVATION);
            $this->accountService->activateAccount($decodedToken['user_id']);
            return $this->re->withMessage('User was activated successfully.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Resend email with activation token.",
        summary: "Resend activation email",
        requestBody: new RequestBody(entityClass: EmailRequest::class),
        tags: [UserController::TAG],
    )]
    #[ResponseMessage(message: "Activation email was sent successfully.")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new NotFoundException(AccountService::ACCOUNT_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[ResponseError(exception: new BadGatewayException())]
    #[Route('/send', name: 'user_activate_send', methods: ['POST'])]
    public function resendToken(EmailRequest $emailRequest): JsonResponse
    {
        try {
            $user = $this->accountService->getAccountByEmail($emailRequest->email);

            $this->mailerService->sendAccountActivation(
                $user->getEmail(),
                $this->jwtService->generateToken($user, JwtUsage::USAGE_ACCOUNT_ACTIVATION)
            );

            return $this->re->withMessage('Activation email was sent successfully.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
