<?php

namespace App\Model\User\UseCase\SignUp\Network;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\User\User;
use App\Model\User\Flusher;

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
            throw new UserCreationException('This user is already exists.');
        }

        $user = new User(
            Id::next(),
            new DateTimeImmutable()
        );

        $user->signUpByNetwork($command->network, $command->identity);
        $this->users->add($user);
        $this->flusher->flush();
    }
}