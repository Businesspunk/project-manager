<?php

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Nonstandard\Uuid;

class Network
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $identity;

    public function __construct(User $user, string $network, string $identity)
    {
        $this->id = Uuid::uuid4();
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getNetwork(): string
    {
        return $this->network;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function isForNetwork(string $network)
    {
        return $this->network === $network;
    }
}