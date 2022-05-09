<?php

namespace App\Model\Work\UseCase\Members\Group\Delete;

use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $groups;
    private $members;
    private $flusher;

    public function __construct(GroupRepository $groups, MemberRepository $members, Flusher $flusher)
    {
        $this->groups = $groups;
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if ($this->members->hasByGroup($this->groups->get($id = new Id($command->id)))) {
            throw new \DomainException('Group is not empty');
        }

        $group = $this->groups->get($id);
        $this->groups->remove($group);
        $this->flusher->flush();
    }
}