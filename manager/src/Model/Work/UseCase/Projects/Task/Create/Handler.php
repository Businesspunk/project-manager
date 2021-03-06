<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\Flusher;

class Handler
{
    private $tasks;
    private $projects;
    private $members;
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        ProjectRepository $projets,
        MemberRepository $members,
        Flusher $flusher
    ) {
        $this->tasks = $tasks;
        $this->projects = $projets;
        $this->members = $members;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $project = $this->projects->get(new ProjectId($command->project));
        $member = $this->members->get(new MemberId($command->author));

        $date = new \DateTimeImmutable();
        foreach ($command->titles as $title) {
            $task = new Task(
                $this->tasks->nextId(),
                $project,
                $member,
                $date,
                $title->value,
                $command->content,
                $command->priority,
                new Type($command->type)
            );

            if ($parent = $command->parent) {
                $parent = $this->tasks->get(new Id($parent));
                $task->setChildOf($parent);
            }

            if ($command->plan) {
                $task->plan($command->plan);
            }
            $this->tasks->add($task);
            $date = $date->modify('+1 second');
        }
        $this->flusher->flush();
    }
}
