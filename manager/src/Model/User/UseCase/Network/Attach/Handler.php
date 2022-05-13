<?php

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\ReadModel\Network\NetworkFetch;

class Handler
{
    private $users;
    private $flusher;
    private $networks;

    public function __construct(UserRepository $users, Flusher $flusher, NetworkFetch $networks)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->networks = $networks;
    }

    public function handle(Command $command)
    {
        $network = $command->network;
        $identity = $command->identity;

        if ($this->networks->existsByNetworkAndIdentity($network, $identity)) {
            throw new \DomainException(sprintf('%s network is already in use', ucfirst($network)));
        }

        $user = $this->users->get(new Id($command->id));
        $user->attachNetwork($network, $identity);
        $this->flusher->flush();
    }
}
