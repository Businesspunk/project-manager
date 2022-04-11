<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Twig\Environment;

class ResetTokenSender
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
        $message = (new \Swift_Message('Reset password'))
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/reset.html.twig', [
                'token' => $token
            ]), 'text/html');

        if (!$this->mailer->send($message)) {
            throw new \DomainException('Unable to send email');
        }
    }
}
