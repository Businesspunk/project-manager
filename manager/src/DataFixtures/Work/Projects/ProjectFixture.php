<?php

namespace App\DataFixtures\Work\Projects;

use App\DataFixtures\Work\Members\MemberFixture;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Status;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_FIRST = 'work_projects_first';
    public const REFERENCE_SECOND = 'work_projects_second';

    public function load(ObjectManager $manager)
    {
        $project = $this->createActiveProject('First', 1);
        $project->addDepartment($developmentId = DepartmentId::next(), 'Development');
        $project->addDepartment($marketingId = DepartmentId::next(), 'Marketing');
        /** @var Member $member */
        $member = $this->getReference(MemberFixture::MEMBER_ADMIN);
        /** @var Role $role */
        $role = $this->getReference(RoleFixture::MANAGER);
        $project->addMember($member, [$marketingId], [$role]);
        /** @var Member $member */
        $member = $this->getReference(MemberFixture::MEMBER_USER);
        /** @var Role $role */
        $role = $this->getReference(RoleFixture::GUEST);
        $project->addMember($member, [$developmentId], [$role]);
        $manager->persist($project);
        $this->setReference(self::REFERENCE_FIRST, $project);

        $project = $this->createActiveProject('Second', 2);
        $project->addDepartment($developmentId = DepartmentId::next(), 'Sales');
        $project->addMember($member, [$developmentId], [$role]);
        $manager->persist($project);
        $this->setReference(self::REFERENCE_SECOND, $project);

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

    public function getDependencies(): array
    {
        return [
            MemberFixture::class,
            RoleFixture::class
        ];
    }
}
