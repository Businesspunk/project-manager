<?php

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{
    private $id;
    private $username;
    private $password;
    private $role;
    private $status;

    public function __construct(string $id, string $username, string $password, string $role, string $status)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    public function getUserIdentifier()
    {
        return $this->getUsername();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function eraseCredentials(): void
    {

    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $user->id === $this->id &&
            $user->username === $this->username &&
            $user->password === $this->password &&
            $user->role === $this->role &&
            $user->status === $this->status;
    }
}