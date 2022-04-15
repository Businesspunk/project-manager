<?php

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\User\User;
use App\Model\User\Flusher;
use DateTimeImmutable;
use App\Model\User\Entity\User\Id;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var Flusher
     */
    private $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if ($this->users->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new \DomainException('This user is already exists.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $command->network,
            $command->identity
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}