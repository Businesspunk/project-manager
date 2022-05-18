<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("work_projects_roles")
 */
class Role
{
    /**
     * @var Id
     * @ORM\Id
     * @ORM\Column(type="work_projects_role_id")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var ArrayCollection|Permission[]
     * @ORM\Column(type="work_projects_role_permissions", nullable=true)
     */
    private $permissions;

    public function __construct(Id $id, string $name, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPermissions(): ArrayCollection
    {
        return $this->permissions;
    }

    public function edit(string $name, array $permissions): void
    {
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    public function hasPermission(string $name): bool
    {
        return $this->permissions->exists(static function (int $key, Permission $value) use ($name) {
            return $value->isNameEqual($name);
        });
    }

    public function clone(Id $id): self
    {
        return new self($id, $this->name, $this->permissions->toArray());
    }

    private function setPermissions(array $permissions): void
    {
        $this->permissions = new ArrayCollection(array_map(static function ($e) {
            return new Permission($e);
        }, $permissions));
    }
}
