<?php

namespace App\Service\Mailer;

use App\Exception\BadGatewayException;
use Exception;
use JsonException;
use MailerSend\Exceptions\MailerSendAssertException;
use MailerSend\Exceptions\MailerSendException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Variable;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class MailerService
{
    /**
     * @var MailerSend $mailerSend
     */
    private MailerSend $mailerSend;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @var string $fromEmail
     */
    private string $fromEmail;

    /**
     * @param string $apiKey
     * @param LoggerInterface $logger
     * @throws MailerSendException
     */
    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->mailerSend = new MailerSend(['api_key' => $apiKey]);
        $this->logger = $logger;
        $this->fromEmail = 'info@soukromekino.cz';
    }

    /**
     * @param string $to
     * @param string $token
     * @return void
     * @throws BadGatewayException
     */
    public function sendAccountActivation(string $to, string $token): void
    {
        $templateId = 'jpzkmgq66dm4059v';
        $dynamicTemplateData = [
            'name' => $to,
            'link' => $_ENV['FRONTEND_URL'] . '/activate?token=' . $token
        ];

        $this->sendEmail($to, $templateId, 'Vítejte na SoukromeKino.cz', $dynamicTemplateData);
    }

    /**
     * @param string $to
     * @param string $token
     * @return void
     * @throws BadGatewayException
     */
    public function sendPasswordReset(string $to, string $token): void
    {
        $templateId = '3yxj6ljwwn5gdo2r';
        $dynamicTemplateData = [
            'name' => $to,
            'link' => $_ENV['FRONTEND_URL'] . '/password-reset?token=' . $token
        ];

        $this->sendEmail($to, $templateId, 'Obnovení hesla', $dynamicTemplateData);
    }

    /**
     * @param string $to
     * @param string $itemName
     * @param string $sender
     * @param string $token
     * @param bool $isFolder
     * @return void
     * @throws BadGatewayException
     */
    public function sendShareItem(string $to, string $itemName, string $sender, string $token, bool $isFolder): void
    {
        $templateId = '3z0vklor88xl7qrx';
        $dynamicTemplateData = [
            'name' => $to,
            'itemName' => $itemName,
            'sender' => $sender,
            'link' => $_ENV['FRONTEND_URL'] . '/share-accept?type=' . ($isFolder ? 'folder' : 'file') . '&token=' . $token
        ];

        $this->sendEmail($to, $templateId, 'Sdílení ' . ($isFolder ? 'složky' : 'souboru'), $dynamicTemplateData);
    }

    /**
     * @param string $to
     * @param string $templateId
     * @param string $subject
     * @param array $dynamicTemplateData
     * @return void
     * @throws BadGatewayException
     */
    private function sendEmail(string $to, string $templateId, string $subject, array $dynamicTemplateData): void
    {
        try {
            $variables = [new Variable($to, $dynamicTemplateData)];
            $recipients = [new Recipient($to, $to)];

            $emailParams = (new EmailParams())
                ->setFrom($this->fromEmail)
                ->setFromName('SoukromeKino')
                ->setRecipients($recipients)
                ->setTemplateId($templateId)
                ->setSubject($subject)
                ->setVariables($variables);

            try {
                $response = $this->mailerSend->email->send($emailParams);
            } catch (JsonException|ClientExceptionInterface|MailerSendAssertException $e) {
                throw new BadGatewayException($e->getMessage());
            }

            $this->logger->debug('Response from MailerSend', ['response' => $response]);

            if (!isset($response['status_code']) || $response['status_code'] >= 400) {
                throw new BadGatewayException('Failed to send email: ' . json_encode($response));
            }

            $this->logger->info('Email was sent successfully.', [
                'to' => $to,
                'templateId' => $templateId,
            ]);

        } catch (Exception $e) {
            throw new BadGatewayException($e->getMessage());
        }
    }
}
