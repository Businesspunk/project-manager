<?php

namespace App\Model\Work\Entity\Projects\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("work_projects_departments")
 */
class Department
{
    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="departments")
     */
    private $project;
    /**
     * @var Id
     * @ORM\Id
     * @ORM\Column(type="work_projects_department_id")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    public function __construct(Project $project, Id $id, string $name)
    {
        $this->project = $project;
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function isEqual(self $other): bool
    {
        return $this->getId()->isEqual($other->getId());
    }

    public function isEqualName(string $name): bool
    {
        return $this->getName() === $name;
    }

    public function edit(string $name)
    {
        $this->name = $name;
    }
}
