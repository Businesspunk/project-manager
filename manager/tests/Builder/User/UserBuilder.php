<?php

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;

class UserBuilder
{
    private $id;
    private $date;

    private $email;
    private $name;
    private $hash;
    private $token;

    private $network;
    private $identity;

    private $confirmed;

    public function __construct(Id $id = null, DateTimeImmutable $date = null, Name $name = null)
    {
        $this->id = $id ?? Id::next();
        $this->name = $name ?? new Name('user', 'user');
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
        $user = null;

        if ($this->email) {
            $user = User::signUpByEmail(
                $this->id,
                $this->date,
                $this->email,
                $this->name,
                $this->hash,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmRegistration();
            }
        }

        if ($this->network) {
            $user = User::signUpByNetwork(
                $this->id,
                $this->date,
                $this->name,
                $this->network,
                $this->identity
            );
        }

        return $user;
    }
}