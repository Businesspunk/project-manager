<?php

namespace App\Model\Work\UseCase\Projects\Task\Edit;

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
     * @Assert\NotBlank
     */
    public $title;
    /**
     * @var string
     */
    public $content;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromTask(Task $task): self
    {
        $result = new self($task->getId()->getValue());
        $result->title = $task->getTitle();
        $result->content = $task->getContent();
        return $result;
    }
}
