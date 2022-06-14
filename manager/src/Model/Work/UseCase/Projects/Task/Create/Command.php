<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Work\Entity\Projects\Task\Type;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $project;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $author;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $title;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $priority;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $type;
    /**
     * @var string
     */
    public $parent;
    /**
     * @var \DateTimeImmutable
     * @Assert\Type("\DateTimeInterface")
     */
    public $plan;

    public function __construct($project, $author)
    {
        $this->project = $project;
        $this->author = $author;
        $this->priority = 2;
        $this->type = Type::NONE;
    }
}
