<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

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
    public $group;
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
    public $firstName;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lastName;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}