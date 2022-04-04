<?php

namespace App\Tests\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Network;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NetworkTest extends KernelTestCase
{
    public function testSuccessSignedUpByNetwork(): void
    {
        $user = (new UserBuilder())->viaNetwork(
            $network = 'vk',
            $identity = '12345'
        )->build();

        $this->assertTrue($user->isActive());
        $this->assertCount(1, $networks = $user->getNetworks());
        $this->assertInstanceOf(Network::class, $first = $networks[0]);
        $this->assertEquals($network, $first->getNetwork());
        $this->assertEquals($identity, $first->getIdentity());
    }

    public function testUserAlreadySignedUpByNetwork(): void
    {
        $user = (new UserBuilder())->viaNetwork(
            $network = 'vk',
            $identity = '12345'
        )->build();

        $this->expectExceptionMessage('This user is already registered');
        $user->signUpByNetwork($network, $identity);
    }
}