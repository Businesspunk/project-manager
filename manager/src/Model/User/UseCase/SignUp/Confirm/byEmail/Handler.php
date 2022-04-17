<?php

namespace App\Model\User\UseCase\SignUp\Confirm\byEmail;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var FLusher
     */
    private $flusher;

    public function __construct(UserRepository $users, FLusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if (!$user = $this->users->findByConfirmationToken($command->token)) {
            throw new \DomainException('No users with this confirmation token');
        }

        $user->confirmRegistration();
        $this->flusher->flush();
    }
}