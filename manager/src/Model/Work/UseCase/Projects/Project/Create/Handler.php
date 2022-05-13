<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Project\Status;
use App\Model\Work\Flusher;

class Handler
{
    private $projects;
    private $flusher;

    public function __construct(ProjectRepository $projects, Flusher $flusher)
    {
        $this->projects = $projects;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $project = new Project(
            Id::next(),
            $command->name,
            $command->sort,
            Status::active()
        );

        $this->projects->add($project);
        $this->flusher->flush();
    }
}
