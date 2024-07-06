<?php

namespace App\Service\Mailer;

use App\Helper\Api\Exception\BadGatewayException;
use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Mail\TypeException;

class MailerService
{
    /**
     * @var SendGrid $sendGrid
     */
    private SendGrid $sendGrid;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @var string $fromEmail
     */
    private string $fromEmail;

    /**
     * @param SendGrid $sendGrid
     * @param LoggerInterface $logger
     */
    public function __construct(SendGrid $sendGrid, LoggerInterface $logger)
    {
        $this->sendGrid = $sendGrid;
        $this->logger = $logger;
        $this->fromEmail = 'info.soukromekino@gmail.com';
    }

    /**
     * @param string $to
     * @param string $token
     * @return void
     * @throws BadGatewayException
     */
    public function sendAccountActivation(string $to, string $token): void
    {
        $templateId = 'd-c5d7091d86324686b4a5f228bac19e74';
        $dynamicTemplateData = [
            'name' => $to,
            'link' => $_ENV['FRONTEND_URL'] . '/activate/' . $token
        ];

        $this->sendEmail($to, $templateId, $dynamicTemplateData);
    }

    /**
     * @param string $to
     * @param string $token
     * @return void
     * @throws BadGatewayException
     */
    public function sendPasswordReset(string $to, string $token): void
    {
        $templateId = 'd-828ba6419b7d41f2a02eb169635a113b';
        $dynamicTemplateData = [
            'name' => $to,
            'link' => $_ENV['FRONTEND_URL'] . '/password-reset/' . $token
        ];

        $this->sendEmail($to, $templateId, $dynamicTemplateData);
    }

    /**
     * @param string $to
     * @param string $templateId
     * @param array $dynamicTemplateData
     * @return void
     * @throws BadGatewayException
     */
    private function sendEmail(string $to, string $templateId, array $dynamicTemplateData): void
    {
        try {
            $email = new Mail();
            $email->setFrom($this->fromEmail, "SoukromeKino");
            $email->addTo($to);
            $email->setTemplateId($templateId);

            foreach ($dynamicTemplateData as $key => $value) {
                $email->addDynamicTemplateData($key, $value);
            }

            $response = $this->sendGrid->send($email);

            if ($response->statusCode() >= 400) {
                throw new BadGatewayException('Failed to send email: ' . $response->body());
            }

            $this->logger->info('Email was sent successfully.', [
                'to' => $to,
                'templateId' => $templateId,
            ]);

        } catch (TypeException $e) {
            throw new BadGatewayException($e->getMessage());
        }
    }
}
