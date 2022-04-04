<?php

namespace App\Tests\Model\User\Entity\User\SignUp;

use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfirmTest extends KernelTestCase
{
    public function testSuccessSignUpConfirmation(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->assertTrue($user->isActive());
        $this->assertNull($user->getToken());
    }

    public function testAlreadyConfirmedSignUp(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->expectExceptionMessage('User is already confirmed.');
        $user->confirmRegistration();
    }
}