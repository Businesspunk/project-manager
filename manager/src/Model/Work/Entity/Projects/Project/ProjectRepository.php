<?php

namespace App\Model\Work\Entity\Projects\Project;

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

    public function get(Id $id): ?Project
    {
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not exist');
        }
        return $project;
    }
}
