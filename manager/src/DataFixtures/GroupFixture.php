<?php

namespace App\DataFixtures;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $group = new Group(
            Id::next(),
            'Group 1'
        );

        $manager->persist($group);
        $manager->flush();
    }
}