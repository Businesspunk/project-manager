<?php

namespace App\Model\User\UseCase\SignUp\Confirm\Manually;

use App\Model\User\Entity\User\Id;
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
        if (!$user = $this->users->find(new Id($command->id))) {
            throw new \DomainException('No users with this id');
        }

        $user->confirmRegistration();
        $this->flusher->flush();
    }
}