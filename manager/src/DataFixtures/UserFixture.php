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
    public const USER_ADMIN = 'user_user_admin';
    public const USER_USER = 'user_user_user';

    private $password_hash;

    public function __construct(PasswordHasher $hasher)
    {
        $this->password_hash = $hasher->hash('password');
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->createUserConfirmedByEmail(
            new Email('admin@app.test'),
            new Name('admin', 'admin')
        );
        $user->changeRole(Role::admin());
        $manager->persist($user);
        $this->setReference(self::USER_ADMIN, $user);

        $user = $this->createUserConfirmedByEmail(
            new Email('user@app.test'),
            new Name('User', 'Confirmed')
        );
        $manager->persist($user);

        $user = $this->createUserByEmail(
            new Email('nonconfirmed@app.test'),
            new Name('User', 'NonConfirmedEmailYet')
        );
        $manager->persist($user);

        $user = $this->createUserByNetwork(
            new Name('User', 'Facebook'),
            'facebook',
            '101'
        );
        $user->requestChangeEmail(new Email('facebook@app.test'), $token = 'token');
        $user->changeEmail($token);
        $manager->persist($user);
        $this->setReference(self::USER_USER, $user);

        $manager->flush();
    }

    private function createUserConfirmedByEmail(Email $email, Name $name): User
    {
        $user = $this->createUserByEmail($email, $name);
        $user->confirmRegistration();
        return $user;
    }

    private function createUserByEmail(Email $email, Name $name): User
    {
        return User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $email,
            $name,
            $this->password_hash,
            'token'
        );
    }

    private function createUserByNetwork(Name $name, string $network, string $identity): User
    {
        return User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $network,
            $identity
        );
    }
}