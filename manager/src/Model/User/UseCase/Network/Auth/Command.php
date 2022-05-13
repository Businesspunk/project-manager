<?php

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    /**
     * @var string
     */
    public $network;
    /**
     * @var string
     */
    public $identity;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $lastName;

    public function __construct(string $network, string $identity, string $firstName, string $lastName)
    {
        $this->network = $network;
        $this->identity = $identity;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
