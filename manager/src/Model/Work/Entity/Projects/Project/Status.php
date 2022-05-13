<?php

namespace App\Model\Work\Entity\Projects\Project;

use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    private $value;

    public function __construct(string $statusName)
    {
        Assert::oneOf($statusName, [
            self::STATUS_ACTIVE,
            self::STATUS_ARCHIVED
        ]);
        $this->value = $statusName;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isActive(): bool
    {
        return $this->value === self::STATUS_ACTIVE;
    }

    public function isArchived(): bool
    {
        return $this->value === self::STATUS_ARCHIVED;
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function archived(): self
    {
        return new self(self::STATUS_ARCHIVED);
    }
}
