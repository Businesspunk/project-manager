<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

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
     * @var array
     * @Assert\NotBlank
     */
    public $members;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        return new self($task->getId()->getValue());
    }
}
