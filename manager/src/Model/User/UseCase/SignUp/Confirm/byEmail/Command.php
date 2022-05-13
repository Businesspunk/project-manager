<?php

namespace App\Model\User\UseCase\SignUp\Confirm\byEmail;

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
