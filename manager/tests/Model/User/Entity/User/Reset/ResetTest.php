<?php

namespace App\Tests\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class ResetTest extends KernelTestCase
{
    public function testSuccessResetPassword()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $now = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user->requestResetPassword($token, $now);
        $this->assertNotNull($user->getResetToken());
        $user->resetPassword($now, $hash = 'new hash');
        $this->assertNotNull($user->getResetToken());
        $this->assertEquals($hash, $user->getPasswordHash());
    }

    public function testFailedResetPasswordDueToExpiredToken()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $now = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+2 day'));
        $user->requestResetPassword($token, $now);
        $this->expectExceptionMessage('Reset token expired.');
        $user->resetPassword($now->modify('+3 day'), 'new hash');
    }

    public function testFailedResetPasswordNotRequested()
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $now = new DateTimeImmutable();
        $this->assertNull($user->getResetToken());
        $this->expectExceptionMessage('Reset password is not requested');
        $user->resetPassword($now, 'new hash');
    }
}