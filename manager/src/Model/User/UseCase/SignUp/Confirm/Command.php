<?php

namespace App\Model\User\UseCase\SignUp\Confirm;

class Command
{
    /**
     * @var string
     */
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
}