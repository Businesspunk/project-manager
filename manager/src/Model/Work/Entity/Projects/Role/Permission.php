<?php

namespace App\Model\Work\Entity\Projects\Role;

use Webmozart\Assert\Assert;

class Permission
{
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';

    private $name;

    public function __construct(string $value)
    {
        Assert::oneOf($value, self::getNames());
        $this->name = $value;
    }

    public static function getNames(): array
    {
        return [
            self::MANAGE_PROJECT_MEMBERS
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNameEqual(string $name): bool
    {
        return $this->getName() === $name;
    }
}
