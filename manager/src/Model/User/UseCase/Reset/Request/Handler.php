<?php

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Flusher;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var ResetTokenizer
     */
    private $tokenizer;
    /**
     * @var ResetTokenSender
     */
    private $sender;
    /**
     * @var Flusher
     */
    private $flusher;

    public function __construct(UserRepository $users, ResetTokenizer $tokenizer, ResetTokenSender $sender, Flusher $flusher)
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $token = $this->tokenizer->generate();
        $user->requestResetPassword($token, new \DateTimeImmutable());
        $this->flusher->flush();
        $this->sender->send($user->getEmail(), $user->getResetToken()->getToken());
    }
}