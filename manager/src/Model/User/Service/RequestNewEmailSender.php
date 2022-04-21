<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Twig\Environment;

class RequestNewEmailSender
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new \Swift_Message('Confirm new email'))
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/request_new_email.html.twig', [
                'token' => $token
            ]), 'text/html');

        if (!$this->mailer->send($message)) {
            throw new \DomainException('Unable to send email');
        }
    }
}