<?php

namespace App\DataFixtures\Work\Members;

use App\DataFixtures\User\UserFixture;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\User\Entity\User\Id as UserId;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Name;
use App\Model\Work\Entity\Members\Member\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
    public const MEMBER_ADMIN = 'work_members_admin';
    public const MEMBER_USER = 'work_members_user';

    public function load(ObjectManager $manager)
    {
        /**
         * @var Group $group
         * @var User $user
         */
        $group = $this->getReference(GroupFixture::GROUP_OUR_STAFF);
        $user = $this->getReference(UserFixture::USER_ADMIN);
        $member = $this->createMember(
            $user->getId(),
            $group,
            new Name($user->getName()->getFirstName(), $user->getName()->getLastName()),
            new Email($user->getEmail()->getValue())
        );
        $this->setReference(self::MEMBER_ADMIN, $member);
        $manager->persist($member);

        /**
         * @var Group $group
         * @var User $user
         */
        $group = $this->getReference(GroupFixture::GROUP_CUSTOMERS);
        $user = $this->getReference(UserFixture::USER_USER);
        $member = $this->createMember(
            $user->getId(),
            $group,
            new Name($user->getName()->getFirstName(), $user->getName()->getLastName()),
            new Email($user->getEmail()->getValue())
        );
        $this->setReference(self::MEMBER_USER, $member);
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

    private function createMember(UserId $id, Group $group, Name $name, Email $email)
    {
        return new Member(
            new Id($id->getValue()),
            $group,
            $name,
            $email,
            Status::active()
        );
    }
}
