<?php

namespace App\Model\User\UseCase\Email\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $token;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}
