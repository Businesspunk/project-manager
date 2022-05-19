<?php

namespace App\DataFixtures\Work\Projects;

use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{
    public const GUEST = 'work_projects_roles_guest';
    public const MANAGER = 'work_projects_roles_manager';

    public function load(ObjectManager $manager)
    {
        $role = $this->createRole('Guest', []);
        $this->setReference(self::GUEST, $role);
        $manager->persist($role);

        $role = $this->createRole('Manager', [Permission::MANAGE_PROJECT_MEMBERS]);
        $this->setReference(self::MANAGER, $role);
        $manager->persist($role);

        $manager->flush();
    }

    private function createRole(string $name, array $permissions): Role
    {
        return new Role(
            Id::next(),
            $name,
            $permissions
        );
    }
}
