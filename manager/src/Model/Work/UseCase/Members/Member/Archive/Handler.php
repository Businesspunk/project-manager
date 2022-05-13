<?php

namespace App\Model\Work\UseCase\Members\Member\Archive;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $members;
    private $flusher;

    public function __construct(MemberRepository $members, Flusher $flusher)
    {
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $member = $this->members->get(new Id($command->id));

        if ($member->isArchived()) {
            throw new \DomainException('Member is already archived');
        }

        $member->archive();
        $this->flusher->flush();
    }
}
