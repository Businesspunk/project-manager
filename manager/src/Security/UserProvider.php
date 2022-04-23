<?php

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $username = $user->getUsername();
        $user = $this->loadUser($username);
        return $this->createIdentityByUser($user, $username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->loadUser($identifier);
        return $this->createIdentityByUser($user, $identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->loadUser($username);
        return $this->createIdentityByUser($user, $username);
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    private function loadUser(string $username): AuthView
    {
        $chunks = explode(':', $username);
        if (count($chunks) == 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if (!$user = $this->users->findForAuthByEmail($username)) {
            throw new UserNotFoundException('');
        }
        return $user;
    }

    private function createIdentityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email ?: $username,
            $user->name ?: $username,
            $user->password_hash,
            $user->role,
            $user->status
        );
    }
}