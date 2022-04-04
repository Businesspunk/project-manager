<?php

namespace App\Model\User\UseCase\Reset\Reset;

class Command
{
    /**
     * @var string
     */
    public $token;
    /**
     * @var string
     */
    public $password;

    public function __construct(string $token, string $password)
    {
        $this->token = $token;
        $this->password = $password;
    }
}