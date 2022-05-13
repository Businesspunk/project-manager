<?php

namespace App\Model\Work\Entity\Projects\Project;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("work_projects_projects")
 */
class Project
{
    /**
     * @var Id
     * @ORM\Id
     * @ORM\Column(type="work_projects_project_id")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;
    /**
     * @var Status
     * @ORM\Column(type="work_projects_project_status")
     */
    private $status;

    public function __construct(Id $id, string $name, int $sort, Status $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = $status;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function reinstate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Project is already active');
        }
        $this->status = Status::active();
    }

    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new \DomainException('Project is already archived');
        }
        $this->status = Status::archived();
    }

    public function edit(string $name, int $sort)
    {
        $this->name = $name;
        $this->sort = $sort;
    }
}
