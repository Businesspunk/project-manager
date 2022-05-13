<?php

namespace App\Model\User\UseCase\Network\Attach;

class Command
{
    public $id;
    public $network;
    public $identity;

    public function __construct(string $id, string $network, string $identity)
    {
        $this->id = $id;
        $this->network = $network;
        $this->identity = $identity;
    }
}
