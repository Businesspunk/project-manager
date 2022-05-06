<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\EntityManagerInterface;

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
}