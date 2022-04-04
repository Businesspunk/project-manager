<?php

namespace App\Model\User\UseCase\Reset;

class Command
{
    /**
     * @var string
     */
    public $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}