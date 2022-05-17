<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Edit;

use App\Model\Work\Entity\Projects\Department\Department;
use App\Model\Work\Entity\Projects\Project\Project;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $projectId;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $departmentId;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $name;

    public function __construct(string $departmentId, string $name)
    {
        $this->departmentId = $departmentId;
        $this->name = $name;
    }

    public static function createFromDepartment(Project $project, Department $department): self
    {
        $entity = new self($department->getId(), $department->getName());
        $entity->projectId = $project->getId();
        return $entity;
    }
}
