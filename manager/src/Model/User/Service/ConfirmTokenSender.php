<?php

namespace App\Model\User\Service;

interface ConfirmTokenSender
{
    public function send(Email $email, string $token): void;
}