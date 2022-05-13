<?php

namespace App\Model\User\UseCase\Create;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordGenerator;

class Handler
{
    private $users;
    private $flusher;
    private $passwordGenerator;

    public function __construct(
        UserRepository $users,
        Flusher $flusher,
        PasswordGenerator $passwordGenerator
    ) {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->passwordGenerator = $passwordGenerator;
    }

    public function handle(Command $command)
    {
        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Email($command->email),
            new Name($command->firstName, $command->lastName),
            $this->passwordGenerator::generate(),
            null
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}
