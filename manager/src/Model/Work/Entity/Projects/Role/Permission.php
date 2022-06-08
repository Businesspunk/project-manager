<?php

namespace App\Model\Work\Entity\Projects\Role;

use Webmozart\Assert\Assert;

class Permission
{
    public const MANAGE_PROJECT_MEMBERS = 'manage_project_members';
    public const VIEW_TASKS = 'view_tasks';
    public const MANAGE_TASKS = 'manage_tasks';

    private $name;

    public function __construct(string $value)
    {
        Assert::oneOf($value, self::getNames());
        $this->name = $value;
    }

    public static function getNames(): array
    {
        return [
            self::MANAGE_PROJECT_MEMBERS,
            self::VIEW_TASKS,
            self::MANAGE_TASKS
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
