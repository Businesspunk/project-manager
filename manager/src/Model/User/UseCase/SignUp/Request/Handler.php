<?php

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Flusher;
use App\Model\User\Service\ConfirmTokenSender;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var PasswordHasher
     */
    private $passwordHasher;
    /**
     * @var Flusher
     */
    private $flusher;
    /**
     * @var ConfirmTokenizer
     */
    private $tokenizer;
    /**
     * @var ConfirmTokenSender
     */
    private $confirmTokenSender;

    public function __construct(
        UserRepository $users,
        PasswordHasher $passwordHasher,
        Flusher $flusher,
        ConfirmTokenizer $tokenizer,
        ConfirmTokenSender $confirmTokenSender
    ) {
        $this->users = $users;
        $this->passwordHasher = $passwordHasher;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->confirmTokenSender = $confirmTokenSender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('This user is already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            $email,
            new Name($command->first_name, $command->last_name),
            $this->passwordHasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);
        $this->confirmTokenSender->send($email, $token);

        $this->flusher->flush();
    }
}
