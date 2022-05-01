<?php

namespace App\Model\User\UseCase\SignUp\Confirm\Manually;

class Command
{
    /**
     * @var string
     */
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}