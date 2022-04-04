<?php

namespace App\Model\User\Service;

interface ResetTokenSender
{
    public function send(Email $email, string $token): void;
}
