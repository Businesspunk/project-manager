<?php

namespace App\Model\User\UseCase\Network\Detach;

class Command
{
    public $user_id;
    public $network;
    public $identity;

    public function __construct($user_id, $network, $identity)
    {
        $this->user_id = $user_id;
        $this->network = $network;
        $this->identity = $identity;
    }
}