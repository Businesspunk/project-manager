<?php

namespace App\Tests\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use DateTimeImmutable;

class RequestTest extends KernelTestCase
{
    public function testUserCreationSuccess(): void
    {
        $user =
            (new UserBuilder(
                $id = Id::next(),
                $date = new DateTimeImmutable()
            ))->viaEmail(
                $email = new Email('n@gmail.com'),
                $password = 'hash123',
                $token = 'token'
            )->build();

        $this->assertTrue($user->isWait());
        $this->assertFalse($user->isActive());

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($date, $user->getDate());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($password, $user->getPasswordHash());
        $this->assertEquals($token, $user->getToken());
        $this->assertTrue($user->getRole()->isUser());
    }
}
