<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Name;
use App\Model\Work\Entity\Members\Member\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /**
         * @var Group $group
         * @var User $user
         */
        $group = $this->getReference(GroupFixture::GROUP_OUR_STAFF);
        $user = $this->getReference(UserFixture::USER_ADMIN);
        $member = $this->createMember(
            $group,
            new Name($user->getName()->getFirstName(), $user->getName()->getLastName()),
            new Email($user->getEmail()->getValue())
        );
        $manager->persist($member);

        /**
         * @var Group $group
         * @var User $user
         */
        $group = $this->getReference(GroupFixture::GROUP_CUSTOMERS);
        $user = $this->getReference(UserFixture::USER_USER);
        $member = $this->createMember(
            $group,
            new Name($user->getName()->getFirstName(), $user->getName()->getLastName()),
            new Email($user->getEmail()->getValue())
        );
        $manager->persist($member);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            GroupFixture::class
        ];
    }

    private function createMember(Group $group, Name $name, Email $email)
    {
        return new Member(
            Id::next(),
            $group,
            $name,
            $email,
            Status::active()
        );
    }
}