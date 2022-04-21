<?php

namespace App\Model\User\UseCase\Email\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
}