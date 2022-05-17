<?php

namespace App\DataFixtures\Work\Projects;

use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $project = $this->createActiveProject('First', 1);
        $project->addDepartment(DepartmentId::next(), 'Development');
        $project->addDepartment(DepartmentId::next(), 'Marketing');
        $manager->persist($project);

        $project = $this->createActiveProject('Second', 2);
        $manager->persist($project);

        $project = $this->createArchivedProject('Third', 3);
        $manager->persist($project);
        $manager->flush();
    }

    private function createActiveProject(string $name, int $sort)
    {
        return new Project(
            Id::next(),
            $name,
            $sort,
            Status::active()
        );
    }

    private function createArchivedProject(string $name, int $sort)
    {
        return new Project(
            Id::next(),
            $name,
            $sort,
            Status::archived()
        );
    }
}
