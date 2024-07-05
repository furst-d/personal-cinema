<?php

namespace App\Service\Mailer;

use App\Helper\Api\Exception\BadGatewayException;
use App\Helper\Mailer\MailBuilder;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    /**
     * @var MailerInterface $mailer

     */
    private MailerInterface $mailer;

    /**3
     * @var MailBuilder $mailBuilder
     */
    private MailBuilder $mailBuilder;

    /**
     * @param MailerInterface $mailer
     * @param MailBuilder $mailBuilder
     */
    public function __construct(MailerInterface $mailer, MailBuilder $mailBuilder)
    {
        $this->mailer = $mailer;
        $this->mailBuilder = $mailBuilder;
    }

    /**
     * @param string $to
     * @param string $token
     * @return void
     * @throws BadGatewayException
     */
    public function sendActivationEmail(string $to, string $token): void
    {
        $subject = 'SoukromeKino - Aktivace účtu';
        $content = 'pro aktivaci účtu klikněte na následující odkaz: <a href="http://localhost:8000/activate/' . $token . '">Aktivovat účet</a>';
        $this->sendEmail($to, $subject, $content);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $content
     * @return void
     * @throws BadGatewayException
     */
    private function sendEmail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('info.soukromekino@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($this->mailBuilder->buildContent($content));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new BadGatewayException($e->getMessage());
        }
    }
}
