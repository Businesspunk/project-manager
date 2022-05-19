<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Department\Department;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table("work_projects_memberships", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"project_id", "member_id"})
 * })
 */
class Membership
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $id;
    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="memberships")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;
    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity=Member::class)
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;
    /**
     * @var ArrayCollection|Department[]
     * @ORM\ManyToMany(targetEntity=Department::class)
     * @ORM\JoinTable(name="work_projects_memberships_departments")
     */
    private $departments;
    /**
     * @var ArrayCollection|Role[]
     * @ORM\ManyToMany(targetEntity=Role::class)
     * @ORM\JoinTable(name="work_projects_memberships_roles")
     */
    private $roles;

    /**
     * @param Project $project
     * @param Member $member
     * @param Department[] $departments
     * @param Role[] $roles
     */
    public function __construct(Project $project, Member $member, array $departments, array $roles)
    {
        $this->guardDepartments($departments);
        $this->guardRoles($roles);
        $this->id = Uuid::uuid4()->toString();
        $this->project = $project;
        $this->member = $member;
        $this->departments = new ArrayCollection($departments);
        $this->roles = new ArrayCollection($roles);
    }

    public function isEqualMember(Member $member): bool
    {
        return $this->member->isEqual($member);
    }

    public function hasDepartment(Department $department): bool
    {
        return $this->departments->exists(static function ($key, Department $item) use ($department) {
            return $item->isEqual($department);
        });
    }

    /**
     * @param Department[] $departments
     */
    public function changeDepartments(array $departments): void
    {
        $this->guardDepartments($departments);

        $current = $this->departments->toArray();
        $new = $departments;

        $compare = static function (Department $a, Department $b): int {
            return $a->getId()->getValue() <=> $b->getId()->getValue();
        };

        foreach (array_udiff($current, $new, $compare) as $item) {
            $this->departments->removeElement($item);
        }

        foreach (array_udiff($new, $current, $compare) as $item) {
            $this->departments->add($item);
        }
    }

    /**
     * @param Role[] $roles
     */
    public function changeRoles(array $roles): void
    {
        $this->guardRoles($roles);
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @param Department[] $departments
     */
    private function guardDepartments(array $departments)
    {
        Assert::minCount($departments, 1);
    }

    /**
     * @param Role[] $roles
     */
    private function guardRoles(array $roles)
    {
        Assert::minCount($roles, 1);
    }
}
