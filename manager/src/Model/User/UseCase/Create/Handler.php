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
    private $tokenizer;

    public function __construct(
        UserRepository $users,
        Flusher $flusher,
        PasswordGenerator $passwordGenerator,
        ConfirmTokenizer $tokenizer
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->passwordGenerator = $passwordGenerator;
        $this->tokenizer = $tokenizer;
    }

    public function handle(Command $command)
    {
        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $email = new Email($command->email),
            new Name($command->firstName, $command->lastName),
            $this->passwordGenerator::generate(),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);
        $this->flusher->flush();
    }
}