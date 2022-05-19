<?php

namespace App\Model\Work\UseCase\Projects\Role\Delete;

use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $roles;
    private $flusher;
    private $projects;

    public function __construct(RoleRepository $roles, ProjectRepository $projects, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $role = $this->roles->get(new Id($command->id));
        if ($this->projects->hasMembersWithRole($role)) {
            throw new \DomainException('Role has members');
        }
        $this->roles->remove($role);
        $this->flusher->flush();
    }
}
