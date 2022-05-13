<?php

namespace App\Tests\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewEmailRequestTest extends KernelTestCase
{
    public function testSuccessRequest()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $user->requestChangeEmail($email = new Email('new@app.test'), $token = 'token');
        $this->assertNotNull($user->getNewEmail());
        $this->assertNotNull($user->getNewEmailToken());
        $this->assertSame($email, $user->getNewEmail());
        $this->assertSame($token, $user->getNewEmailToken());
    }

    public function testFailedTheSameEmail()
    {
        $user = (new UserBuilder())->viaEmail($email = new Email('new@app.test'))->confirm()->build();
        $this->expectExceptionMessage('Email is the same');
        $user->requestChangeEmail($email, $token = 'token');
    }

    public function testFailedUserIsNotActive()
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $this->expectExceptionMessage('User is not active');
        $user->requestChangeEmail(new Email('new@app.test'), $token = 'token');
    }
}
