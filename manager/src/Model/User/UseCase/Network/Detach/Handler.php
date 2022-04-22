<?php

namespace App\Model\User\UseCase\Network\Detach;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;

class Handler
{
    private $users;
    private $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $network = $command->network;

        $user = $this->users->get(new Id($command->user_id));
        $user->dettachNetwork($network);
        $this->flusher->flush();
    }
}