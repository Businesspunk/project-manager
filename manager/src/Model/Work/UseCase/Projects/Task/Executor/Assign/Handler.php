<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Flusher;
use Doctrine\Common\Collections\ArrayCollection;

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
        $currentExecutors = $task->getExecutors()->toArray();

        /** @var Member $member */
        foreach ($members as $member) {
            if ($this->inInListOfExecutors($member, $currentExecutors)) {
                continue;
            }
            $task->assignExecutor($member);
        }

        $this->flusher->flush();
    }

    private function inInListOfExecutors(Member $member, array $executors): bool
    {
        foreach ($executors as $executor) {
            if ($member->isEqual($executor)) {
                return true;
            }
        }
        return false;
    }
}
