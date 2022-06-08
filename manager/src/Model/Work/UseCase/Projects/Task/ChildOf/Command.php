<?php

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use App\Model\Work\Entity\Projects\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var string
     */
    public $parent;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        $command = new self($task->getId()->getValue());
        $command->parent = ($parent = $task->getParent()) ? $parent->getId()->getValue() : null;
        return $command;
    }
}
