<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

use App\Model\Work\Entity\Projects\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var int
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var \DateTimeImmutable
     * @Assert\NotBlank
     * @Assert\Type("\DateTimeInterface")
     */
    public $plan;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        $command = new self($task->getId()->getValue());
        $command->plan = $task->getPlanDate();
        return $command;
    }
}
