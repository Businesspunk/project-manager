<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class RoleRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Role::class);
    }

    public function findAll(): array
    {
        return $this->repo->findAll();
    }

    public function add(Role $role): void
    {
        $this->em->persist($role);
    }

    public function remove(Role $role): void
    {
        $this->em->remove($role);
    }

    public function find(Id $id): ?Role
    {
        return $this->repo->find($id->getValue());
    }

    public function get(Id $id): ?Role
    {
        if (!$role = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Role does not exist');
        }
        return $role;
    }
}
