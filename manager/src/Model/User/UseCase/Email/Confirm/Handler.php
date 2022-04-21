<?php

namespace App\Model\User\UseCase\Email\Confirm;

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
        $user = $this->users->get(new Id($command->id));
        if ($user->getNewEmailToken() !== $command->token) {
            throw new \DomainException('You can\'t apply this token');
        }

        $user->changeEmail($command->token);
        $this->flusher->flush();
    }
}