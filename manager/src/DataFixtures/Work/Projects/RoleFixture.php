<?php

namespace App\DataFixtures\Work\Projects;

use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $role = $this->createRole('Guest', []);
        $manager->persist($role);

        $role = $this->createRole('Manager', [Permission::MANAGE_PROJECT_MEMBERS]);
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
