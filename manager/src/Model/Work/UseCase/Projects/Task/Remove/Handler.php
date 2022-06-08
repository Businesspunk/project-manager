<?php

namespace App\Model\Work\UseCase\Projects\Task\Remove;

use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $tasks;
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        Flusher $flusher
    ) {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $task = $this->tasks->get(new Id($command->id));
        $this->tasks->remove($task);
        $this->flusher->flush();
    }
}
