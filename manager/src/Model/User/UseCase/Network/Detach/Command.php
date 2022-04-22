<?php

namespace App\Model\User\UseCase\Network\Detach;

class Command
{
    public $user_id;
    public $network;

    public function __construct($user_id, $network)
    {
        $this->user_id = $user_id;
        $this->network = $network;
    }
}