<?php

namespace App\Model\Work\UseCase\Projects\Role\Copy;

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

        $current = $this->roles->get(new Id($command->id));
        $role = $current->clone(
            Id::next(),
            $name
        );
        $this->roles->add($role);
        $this->flusher->flush();
    }
}
