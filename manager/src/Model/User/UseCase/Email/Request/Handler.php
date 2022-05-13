<?php

namespace App\Model\User\UseCase\Email\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\Model\User\Service\RequestNewEmailSender;
use App\Model\User\Service\RequestNewEmailTokenizer;

class Handler
{
    private $users;
    private $flusher;
    private $tokenizer;
    private $sender;

    public function __construct(
        UserRepository $users,
        Flusher $flusher,
        RequestNewEmailTokenizer $tokenizer,
        RequestNewEmailSender $sender
    ) {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command)
    {
        $email = new Email($command->email);

        if ($this->users->findByEmail($email)) {
            throw new \DomainException('This email is already in use');
        }

        $user = $this->users->get(new Id($command->id));
        $token = $this->tokenizer->generate();
        $user->requestChangeEmail($email, $token);
        if ($user->getEmail()) {
            $this->sender->send($email, $token);
        } else {
            $user->changeEmail($token);
        }
        $this->flusher->flush();
    }
}
