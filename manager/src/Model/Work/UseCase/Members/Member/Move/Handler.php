<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $members;
    private $groups;
    private $flusher;

    public function __construct(MemberRepository $members, GroupRepository $groups, Flusher $flusher)
    {
        $this->members = $members;
        $this->groups = $groups;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $member = $this->members->get(new Id($command->id));
        $group = $this->groups->get(new GroupId($command->group));

        if ($member->getGroup()->isEqualId($group)) {
            throw new \DomainException('Member is already in this group');
        }

        $member->move($group);
        $this->flusher->flush();
    }
}