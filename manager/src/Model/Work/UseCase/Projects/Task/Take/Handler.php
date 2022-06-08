<?php

namespace App\Model\Work\UseCase\Projects\Task\Take;

use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $tasks;
    private $members;
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        MemberRepository $members,
        Flusher $flusher
    ) {
        $this->tasks = $tasks;
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $task = $this->tasks->get(new Id($command->id));
        $member = $this->members->get(new MemberId($command->member));

        if (!$task->hasExecutor($member)) {
            $task->assignExecutor($member);
        }

        $this->flusher->flush();
    }
}
