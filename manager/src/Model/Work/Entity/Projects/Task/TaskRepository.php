<?php

namespace App\Model\Work\Entity\Projects\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class TaskRepository
{
    private $em;
    private $repo;
    private $connection;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Task::class);
        $this->connection = $em->getConnection();
    }

    public function nextId(): Id
    {
        return new Id($this->connection->query('SELECT nextval(\'work_projects_tasks_seq\')')->fetchColumn());
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
    }

    public function get(Id $id): ?Task
    {
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not exist');
        }
        return $project;
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
    }

    public function getAllByParent(Id $id): ?array
    {
        return $this->repo->findBy(['parent' => $id->getValue()]);
    }
}
