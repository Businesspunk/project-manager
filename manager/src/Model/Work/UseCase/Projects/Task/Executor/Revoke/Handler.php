<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Revoke;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Id;
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
        $members = $this->members->getByIds($command->members);

        /** @var Member $member */
        foreach ($members as $member) {
            $task->revokeExecutor($member);
        }

        $this->flusher->flush();
    }
}
