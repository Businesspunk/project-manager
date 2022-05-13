<?php

namespace App\Model\Work\UseCase\Projects\Project\Edit;

use App\Model\Work\Entity\Projects\Project\Project;
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
    public $name;
    /**
     * @var integer
     * @Assert\NotBlank
     */
    public $sort;

    public function __construct(string $id, string $name, int $sort)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
    }

    public static function createFromProject(Project $project): self
    {
        return new self($project->getId(), $project->getName(), $project->getSort());
    }
}
