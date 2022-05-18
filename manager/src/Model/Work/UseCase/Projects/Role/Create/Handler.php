<?php

namespace App\Model\Work\UseCase\Projects\Role\Create;

use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\Role;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $roles;
    private $flusher;

    public function __construct(RoleRepository $roles, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if ($this->roles->hasByName($name = $command->name)) {
            throw new \DomainException('Name is already in use');
        }

        $role = new Role(
            Id::next(),
            $name,
            $command->permissions
        );

        $this->roles->add($role);
        $this->flusher->flush();
    }
}
