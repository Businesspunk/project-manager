<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class GroupRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Group::class);
    }

    public function add(Group $group): void
    {
        $this->em->persist($group);
    }

    public function remove(Group $group): void
    {
        $this->em->remove($group);
    }

    public function get(Id $id): ?Group
    {
        if (!$group = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Group is not exist');
        }
        return $group;
    }
}