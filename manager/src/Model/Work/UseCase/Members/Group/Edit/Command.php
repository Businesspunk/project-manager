<?php

namespace App\Model\Work\UseCase\Members\Group\Edit;

use App\Model\Work\Entity\Members\Group\Group;
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
    public $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromGroup(Group $group): self
    {
        return new self($group->getId(), $group->getName());
    }
}