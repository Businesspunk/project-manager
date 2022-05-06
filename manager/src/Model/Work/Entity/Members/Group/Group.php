<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="work_members_groups")
 */
class Group
{
    /**
     * @var Id
     * @ORM\Column (type="work_members_group_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     * @ORM\Column (type="string", nullable=false)
     */
    private $name;

    public function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function edit(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): Id
    {
        return $this->id;
    }
}