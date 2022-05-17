<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Projects\Department\Department;
use Doctrine\Common\Collections\ArrayCollection;
use App\Model\Work\Entity\Projects\Department\Id as DepartmentId;
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
    /**
     * @var ArrayCollection|Department
     * @ORM\OneToMany(targetEntity=Department::class, mappedBy="project", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $departments;

    public function __construct(Id $id, string $name, int $sort, Status $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = $status;
        $this->departments = new ArrayCollection();
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

    public function addDepartment(DepartmentId $id, string $name)
    {
        foreach ($this->departments as $department) {
            if ($department->isEqualName($name)) {
                throw new \DomainException('Department already exists');
            }
        }
        $this->departments->add(new Department($this, $id, $name));
    }

    public function editDepartment(DepartmentId $id, string $name)
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                $department->edit($name);
                return;
            }
        }
        throw new \DomainException('Department is not found');
    }

    public function removeDepartment(DepartmentId $id)
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                $this->departments->removeElement($department);
                return;
            }
        }
        throw new \DomainException('Department is not found');
    }

    /**
     * @return Department|ArrayCollection
     */
    public function getDepartments()
    {
        return $this->departments;
    }
}
