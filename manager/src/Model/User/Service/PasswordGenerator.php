<?php

namespace App\Model\User\Service;

use Ramsey\Uuid\Uuid;

class PasswordGenerator
{
    public static function generate()
    {
        return Uuid::uuid4();
    }
}