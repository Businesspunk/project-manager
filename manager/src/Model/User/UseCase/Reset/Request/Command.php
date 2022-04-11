<?php

namespace App\Model\User\UseCase\Reset\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @var string
     */
    public $email;
}