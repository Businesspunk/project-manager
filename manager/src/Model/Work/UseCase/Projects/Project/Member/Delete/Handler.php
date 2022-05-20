<?php

namespace App\Model\Work\UseCase\Projects\Project\Member\Delete;

use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $projects;
    private $members;
    private $flusher;

    public function __construct(ProjectRepository $projects, MemberRepository $members, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $project = $this->projects->get(new Id($command->project));
        $member = $this->members->get(new MemberId($command->member));
        $project->removeMember($member);
        $this->flusher->flush();
    }
}
