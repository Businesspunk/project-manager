<?php

namespace App\Model\User\Service;

class PasswordHasher
{
    public function hash($password)
    {
        $hash = password_hash($password, PASSWORD_ARGON2I, ['cost' => 12]);
        if ($hash === false){
            throw new \RuntimeException('Password hash Exception');
        }

        return $hash;
    }

    public function validate(string $password, string $hash)
    {
        return password_verify($password, $hash);
    }
}