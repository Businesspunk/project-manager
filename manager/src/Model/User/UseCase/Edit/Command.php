<?php

namespace App\Model\User\UseCase\Edit;

use App\Model\User\Entity\User\User;
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

    public function __construct(string $id, string $firstName, string $lastName, string $email)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }

    public static function createFromUser(User $user): self
    {
        $name = $user->getName();

        return new self(
            $user->getId(),
            $name->getFirstName(),
            $name->getLastName(),
            $user->getEmail()
        );
    }
}