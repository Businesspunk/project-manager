<?php

namespace App\DataFixtures;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Model\User\Entity\User\User;

class UserFixture extends Fixture
{
    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $hash = $this->hasher->hash('password');

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('admin@app.test'),
            new Name('admin', 'admin'),
            $hash,
            'token'
        );

        $user->confirmRegistration();
        $user->changeRole(Role::admin());

        $manager->persist($user);
        $manager->flush();
    }
}