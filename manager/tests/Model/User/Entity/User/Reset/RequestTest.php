<?php

namespace App\Tests\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class RequestTest extends KernelTestCase
{
    public function testSuccessSetResetToken()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $token = new ResetToken('token', $date = new DateTimeImmutable());
        $user->requestResetPassword($token, $date->modify('+1 day'));
        $this->assertNotNull($user->getResetToken());
    }

    public function testFailedSetResetTokenDueToNotEndedExpirationPeriod()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $now = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+3 day'));
        $user->requestResetPassword($token, $now->modify('+1 day'));

        $this->expectExceptionMessage('Previous reset token is not expired.');

        $token2 = new ResetToken('token', $now->modify('+4 day'));
        $user->requestResetPassword($token2, $now->modify('+2 day'));
    }

    public function testSuccessSetResetTokenWhenEndedExpirationPeriod()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $now = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+3 day'));
        $user->requestResetPassword($token, $now->modify('+1 day'));

        $token2 = new ResetToken('token', $now->modify('+4 day'));
        $user->requestResetPassword($token2, $now->modify('+3 day'));
        $this->assertEquals($token2, $user->getResetToken());
    }

    public function testFailedSetResetTokenDueToEmail()
    {
        $user = (new UserBuilder())->viaNetwork()->build();
        $token = new ResetToken('token', $date = new DateTimeImmutable());
        $this->expectExceptionMessage('Email is not specified.');
        $user->requestResetPassword($token, $date->modify('+1 day'));
    }

    public function testFailedSetResetTokenDueToUnconfirmedAccount()
    {
        $user = (new UserBuilder())->viaEmail()->build();
        $now = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $this->expectExceptionMessage('User is not active');
        $user->requestResetPassword($token, $now);
    }
}