<?php

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Work\Entity\Projects\Role\Role;
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
    /**
     * @var array
     */
    public $permissions = [];

    public function __construct(string $id, string $name, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public static function createFromRole(Role $role): self
    {
        return new self($role->getId(), $role->getName(), $role->getNamesOfPermissions());
    }
}
