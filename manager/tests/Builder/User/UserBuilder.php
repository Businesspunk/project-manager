<?php

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;

class UserBuilder
{
    private $id;
    private $date;

    private $email;
    private $hash;
    private $token;

    private $network;
    private $identity;

    private $confirmed;

    public function __construct(Id $id = null, DateTimeImmutable $date = null)
    {
        $this->id = $id ?? Id::next();
        $this->date = $date ?? new DateTimeImmutable();
    }

    public function viaEmail(Email $email = null, string $hash = 'hash', $token = 'token'): self
    {
        $builder = clone $this;
        $builder->email = $email ?? new Email('email@gmail.com');
        $builder->hash = $hash;
        $builder->token = $token;
        return $builder;
    }

    public function viaNetwork(string $network = 'vk', string $identity = '1234'): self
    {
        $builder = clone $this;
        $builder->network = $network;
        $builder->identity = $identity;
        return $builder;
    }

    public function confirm(): self
    {
        $builder = clone $this;
        $builder->confirmed = true;
        return $builder;
    }

    public function build()
    {
        $user = new User($this->id, $this->date);

        if ($this->email) {
            $user->signUpByEmail(
                $this->email,
                $this->hash,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmRegistration();
            }
        }

        if ($this->network) {
            $user->signUpByNetwork(
                $this->network,
                $this->identity
            );
        }

        return $user;
    }
}