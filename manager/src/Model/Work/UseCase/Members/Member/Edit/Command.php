<?php

namespace App\Model\Work\UseCase\Members\Member\Edit;

use App\Model\Work\Entity\Members\Member\Member;
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

    public function __construct(string $id, string $email, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public static function createFromMember(Member $member): self
    {
        return new self(
            $member->getId(),
            $member->getEmail(),
            $member->getName()->getFirstName(),
            $member->getName()->getLastName()
        );
    }
}