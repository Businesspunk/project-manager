<?php

namespace App\DataFixtures;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
    public const GROUP_OUR_STAFF = 'work_members_group_staff';
    public const GROUP_CUSTOMERS = 'work_members_group_customers';

    public function load(ObjectManager $manager)
    {
        $group = new Group(
            Id::next(),
            'Our staff'
        );
        $manager->persist($group);
        $this->setReference(self::GROUP_OUR_STAFF, $group);

        $group = new Group(
            Id::next(),
            'Customers'
        );
        $manager->persist($group);
        $this->setReference(self::GROUP_CUSTOMERS, $group);

        $manager->flush();
    }
}
