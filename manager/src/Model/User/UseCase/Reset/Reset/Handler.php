<?php

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var PasswordHasher
     */
    private $hasher;
    /**
     * @var Flusher
     */
    private $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if (!$user = $this->users->findByResetToken($command->token)) {
            throw new \DomainException('No users with this token');
        }

        $user->resetPassword(
            new \DateTimeImmutable(),
            $this->hasher->hash($command->password)
        );
        $this->flusher->flush();
    }
}
