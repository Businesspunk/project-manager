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

    public function testSuccessDetachNetwork()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();
        $user->attachNetwork($network = 'facebook',  $identity ='123');
        $user->attachNetwork($network2 = 'vk', $identity2 = '124');
        $this->assertCount(2, $user->getNetworks());
        $user->detachNetwork($network, $identity);
        $this->assertCount(1, $user->getNetworks());
        $networks = $user->getNetworks();
        $this->assertEquals($network2, reset($networks)->getNetwork());
        $this->assertEquals($identity2, reset($networks)->getIdentity());
        $user->detachNetwork($network2, $identity2);
        $this->assertCount(0, $user->getNetworks());
    }

    public function testFailedTryToDetachLastIdentity()
    {
        $user = (new UserBuilder())->viaNetwork(
            $network = 'facebook',
            $identity = '123'
        )->build();

        $this->expectExceptionMessage('You can not detach last identity');
        $user->detachNetwork($network, $identity);
    }

    public function testFailedTryToDetachNotExistingNetwork()
    {
        $user = (new UserBuilder())->viaNetwork(
            $network = 'facebook',
            $identity = '123'
        )->build();

        $this->expectExceptionMessage('This network is not exist');
        $user->detachNetwork('vk', '125');
    }
}