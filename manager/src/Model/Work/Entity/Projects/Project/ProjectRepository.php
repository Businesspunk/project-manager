<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ProjectRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Project::class);
    }

    public function get(Id $id): ?Project
    {
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not exist');
        }
        return $project;
    }

    public function getDepartments(Project $project): ?array
    {
        // @ToDo get Member Email from Query
        $stmt = $this->repo->createQueryBuilder('t')
                ->select('t.id as t1', 'membership.id as t2', 'membership.member as t3')
                ->innerJoin('t.memberships', 'membership')
                ->getQuery()
                ->execute();

        return $stmt;
    }

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }

    public function find(Id $id): ?Project
    {
        return $this->repo->find($id->getValue());
    }

    public function hasMembersWithRole(Role $role)
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.memberships', 'm')
                ->innerJoin('m.roles', 'r')
                ->andWhere('r.id = :role')
                ->setParameter(':role', $role->getId()->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }
}
