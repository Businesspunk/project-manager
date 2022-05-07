<?php

namespace App\Model\Work\UseCase\Members\Group\Delete;

use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id;
use App\Model\Work\Flusher;

class Handler
{
    private $groups;
    private $flusher;

    public function __construct(GroupRepository $groups, Flusher $flusher)
    {
        $this->groups = $groups;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $group = $this->groups->get(new Id($command->id));
        $this->groups->remove($group);
        $this->flusher->flush();
    }
}