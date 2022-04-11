<?php

namespace App\Model\User\UseCase\Reset\Reset;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    public $token;
    /**
     * @Assert\NotBlank
     * @Assert\Length (min=6)
     * @var string
     */
    public $password;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}