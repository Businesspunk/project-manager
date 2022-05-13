<?php

namespace App\Tests\Model\User\Entity\User\Name;

use App\Model\User\Entity\User\Name;
use App\Tests\Builder\User\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NameTest extends KernelTestCase
{
    public function testSuccessChangeName()
    {
        $user = (new UserBuilder())->viaEmail()->confirm()->build();

        $newFirstName = 'newFirstNameUser';
        $newLastName = 'newLastNameUser';
        $user->changeName(new Name($newFirstName, $newLastName));
        $this->assertEquals($newFirstName, $user->getName()->getFirstName());
        $this->assertEquals($newLastName, $user->getName()->getLastName());
    }
}
