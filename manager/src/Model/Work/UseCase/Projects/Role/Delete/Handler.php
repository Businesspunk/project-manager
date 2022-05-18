<?php

namespace App\Model\Work\UseCase\Projects\Role\Delete;

use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $role;
    private $flusher;

    public function __construct(RoleRepository $role, Flusher $flusher)
    {
        $this->role = $role;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $role = $this->role->get(new Id($command->id));
        $this->role->remove($role);
        $this->flusher->flush();
    }
}
