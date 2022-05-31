<?php

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Status
{
    private const NEW = 'new';
    private const IN_WORK = 'in work';
    private const NEED_APPROVE = 'need approve';
    private const DONE = 'done';

    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::NEW,
            self::IN_WORK,
            self::NEED_APPROVE,
            self::DONE
        ]);

        $this->value = $value;
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public static function inWork(): self
    {
        return new self(self::IN_WORK);
    }

    public static function needApprove(): self
    {
        return new self(self::NEED_APPROVE);
    }

    public static function done(): self
    {
        return new self(self::DONE);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function isNew(): bool
    {
        return $this->value === self::NEW;
    }

    public function isDone(): bool
    {
        return $this->value === self::DONE;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
