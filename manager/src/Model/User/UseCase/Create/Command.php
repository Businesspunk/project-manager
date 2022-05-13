<?php

namespace App\Model\User\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $firstName;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lastName;
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;
}
