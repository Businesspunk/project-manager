<?php

namespace App\Tests\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ActivateTest extends KernelTestCase
{
    public function testSuccess()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isBlocked());
        $user->block();
        $this->assertTrue($user->isBlocked());
        $this->assertFalse($user->isActive());
        $user->activate();
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isBlocked());
    }

    public function testFail()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->expectExceptionMessage('User is already active');
        $user->activate();
    }
}
