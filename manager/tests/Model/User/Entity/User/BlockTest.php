<?php

namespace App\Tests\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlockTest extends KernelTestCase
{
    public function testSuccess()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->assertTrue($user->isActive());
        $user->block();
        $this->assertTrue($user->isBlocked());
        $this->assertFalse($user->isActive());
    }

    public function testFail()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $this->assertTrue($user->isActive());
        $user->block();
        $this->expectExceptionMessage('User is already blocked');
        $user->block();
    }
}
