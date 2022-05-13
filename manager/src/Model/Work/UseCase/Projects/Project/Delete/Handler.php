<?php

namespace App\Model\Work\UseCase\Projects\Project\Delete;

use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
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
        $project = $this->projects->get(new Id($command->id));
        $this->projects->remove($project);
        $this->flusher->flush();
    }
}
