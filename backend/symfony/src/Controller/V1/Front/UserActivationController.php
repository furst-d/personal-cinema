<?php

namespace App\Controller\V1\Front;

use App\Controller\ApiController;
use App\DTO\Account\EmailRequest;
use App\DTO\Account\TokenRequest;
use App\Helper\Api\Exception\BadGatewayException;
use App\Helper\Api\Exception\BadRequestException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Api\Exception\NotFoundException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Account\AccountService;
use App\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
        BaseControllerLocator    $locator,
        AccountService         $accountService,
        JwtService             $jwtService,
        MailerService          $mailerService,
    ) {
        parent::__construct($locator);
        $this->accountService = $accountService;
        $this->jwtService = $jwtService;
        $this->mailerService = $mailerService;
    }

    /**
     * @param TokenRequest $tokenRequest
     * @return JsonResponse
     */
    #[Route('', name: 'user_activate', methods: ['POST'])]
    public function activate(TokenRequest $tokenRequest): JsonResponse
    {
        try {
            $decodedToken = $this->jwtService->decodeToken($tokenRequest->token, JwtUsage::USAGE_ACCOUNT_ACTIVATION);
            $this->accountService->activateAccount($decodedToken['user_id']);
            return $this->re->withMessage('User was activated successfully.');
        } catch (BadRequestException|NotFoundException $e) {
            return $this->re->withException($e);
        }
    }

    /**
     * @param EmailRequest $emailRequest
     * @return JsonResponse
     */
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
        } catch (NotFoundException|BadGatewayException|InternalException $e) {
            return $this->re->withException($e);
        }
    }
}
