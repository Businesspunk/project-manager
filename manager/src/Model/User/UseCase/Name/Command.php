<?php

namespace App\Model\User\UseCase\Name;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $newFirstName;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $newLastName;
}