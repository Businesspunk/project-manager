<?php

namespace App\Tests\Model\User\Entity\User\Role;

use App\Model\User\Entity\User\Role;
use App\Tests\Builder\User\UserBuilder;
use SebastianBergmann\CodeCoverage\Node\Builder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleTest extends KernelTestCase
{
    public function testSuccessChangeRole()
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->changeRole(Role::admin());

        $this->assertFalse($user->getRole()->isUser());
        $this->assertTrue($user->getRole()->isAdmin());
    }

    public function testFailedAssignedTheSameRole()
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $this->expectExceptionMessage('The role is already the same');
        $user->changeRole(Role::user());
    }
}
