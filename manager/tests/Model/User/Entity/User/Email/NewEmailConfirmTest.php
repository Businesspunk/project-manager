<?php

namespace App\Tests\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewEmailConfirmTest extends KernelTestCase
{
    public function testSuccessRequest()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $user->requestChangeEmail($email = new Email('new@app.test'), $token = 'token');
        $user->changeEmail($token);
        $this->assertSame($email, $user->getEmail());
        $this->assertNull($user->getNewEmail());
        $this->assertNull($user->getNewEmailToken());
    }

    public function testFailedNewEmailWasNotRequested()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->expectExceptionMessage('Change email was not requested');
        $user->changeEmail('token');
    }

    public function testFailedWrongToken()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $user->requestChangeEmail(new Email('new@app.test'), $token = 'token');
        $this->expectExceptionMessage('Token is invalid');
        $user->changeEmail('token_another');
    }
}
