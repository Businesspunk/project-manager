<?php

namespace App\Model\User\UseCase\Role;

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
    public $role;

    public function __construct(string $id, string $role)
    {
        $this->id = $id;
        $this->role = $role;
    }

    public static function createFromUser(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getRole()
        );
    }
}
