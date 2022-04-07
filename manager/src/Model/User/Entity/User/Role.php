<?php

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    private const ROLE_USER = 'ROLE_USER';
    private const ROLE_ADMIN = 'ROLE_ADMIN';
    /**
     * @var string
     */
    private $name;

    public function __construct(string $rolename)
    {
        Assert::oneOf($rolename, [
            self::ROLE_USER,
            self::ROLE_ADMIN
        ]);

        $this->name = $rolename;
    }

    public static function user(): self
    {
        return new self(self::ROLE_USER);
    }

    public static function admin(): self
    {
        return new self(self::ROLE_ADMIN);
    }

    public function isUser(): bool
    {
        return $this->name === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ROLE_ADMIN;
    }

    public function isEqual(self $role): bool
    {
        return $this->getName() === $role->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}