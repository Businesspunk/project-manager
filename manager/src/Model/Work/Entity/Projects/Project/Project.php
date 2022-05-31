<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Department\Department;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Common\Collections\ArrayCollection;
use App\Model\Work\Entity\Projects\Department\Id as DepartmentId;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use Doctrine\Common\Collections\Collection;
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
    /**
     * @var ArrayCollection|Membership
     * @ORM\OneToMany(targetEntity="Membership", mappedBy="project", cascade={"persist"}, orphanRemoval=true)
     */
    private $memberships;

    public function __construct(Id $id, string $name, int $sort, Status $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sort = $sort;
        $this->status = $status;
        $this->departments = new ArrayCollection();
        $this->memberships = new ArrayCollection();
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

    public function getDepartment(DepartmentId $id): Department
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                return $department;
            }
        }
        throw new \DomainException('Department does not exist');
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function getMemberships()
    {
        return $this->memberships;
    }

    public function getMembershipByMember(Member $member): Membership
    {
        $member = $this->findMembershipByMember($member);
        if (!is_null($member)) {
            return $member;
        }
        throw new \DomainException('Member does not exist');
    }

    public function isEqual(self $other): bool
    {
        return $this->id->isEqual($other->id);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isMemberGranted(MemberId $id, string $permission): bool
    {
        $membership = $this->findMembershipByMemberId($id);
        if (!$membership instanceof Membership) {
            return false;
        }

        return $membership->hasPermission($permission);
    }

    public function hasMember(MemberId $id): bool
    {
        return $this->findMembershipByMemberId($id) instanceof Membership;
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

    public function edit(string $name, int $sort): void
    {
        $this->name = $name;
        $this->sort = $sort;
    }

    public function addDepartment(DepartmentId $id, string $name): void
    {
        /** @var Department $department */
        foreach ($this->departments as $department) {
            if ($department->isEqualName($name)) {
                throw new \DomainException('Department already exists');
            }
        }
        $this->departments->add(new Department($this, $id, $name));
    }

    public function editDepartment(DepartmentId $id, string $name): void
    {
        $department = $this->getDepartment($id);
        $department->edit($name);
    }

    public function removeDepartment(DepartmentId $id): void
    {
        $department = $this->getDepartment($id);
        /** @var Membership $membership */
        foreach ($this->memberships as $membership) {
            if ($membership->hasDepartment($department)) {
                throw new \DomainException('Department has members');
            }
        }
        $this->departments->removeElement($department);
    }

    /**
     * @param Member $member
     * @param DepartmentId[] $departmentIds
     * @param Role[] $roles
     */
    public function addMember(Member $member, array $departmentIds, array $roles): void
    {
        if (!is_null($this->findMembershipByMember($member))) {
            throw new \DomainException('Member is already attached');
        }

        $departments = array_map([$this, 'getDepartment'], $departmentIds);
        $membership = new Membership($this, $member, $departments, $roles);
        $this->memberships->add($membership);
    }

    /**
     * @param Member $member
     * @param DepartmentId[] $departmentIds
     * @param Role[] $roles
     */
    public function editMember(Member $member, array $departmentIds, array $roles): void
    {
        $membership = $this->getMembershipByMember($member);
        $membership->changeDepartments(array_map([$this, 'getDepartment'], $departmentIds));
        $membership->changeRoles($roles);
    }

    public function removeMember(Member $member): void
    {
        $membership = $this->getMembershipByMember($member);
        $this->memberships->removeElement($membership);
    }

    private function findMembershipByMember(Member $member): ?Membership
    {
        /** @var Membership $membership */
        foreach ($this->memberships as $membership) {
            if ($membership->isEqualMember($member)) {
                return $membership;
            }
        }
        return null;
    }

    private function findMembershipByMemberId(MemberId $id): ?Membership
    {
        /** @var Membership $membership */
        foreach ($this->memberships as $membership) {
            if ($membership->isEqualMemberId($id)) {
                return $membership;
            }
        }
        return null;
    }
}
