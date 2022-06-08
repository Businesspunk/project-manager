<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

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
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        ProjectRepository $projets,
        Flusher $flusher
    ) {
        $this->tasks = $tasks;
        $this->projects = $projets;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $task = $this->tasks->get(new Id($command->id));
        $project = $this->projects->get(new ProjectId($command->project));
        $task->move($project);

        if ($command->withChildren) {
            $children = $this->tasks->getAllByParent($task->getId());
            /** @var Task $child */
            foreach ($children as $child) {
                $child->move($project);
            }
        }

        $this->flusher->flush();
    }
}
