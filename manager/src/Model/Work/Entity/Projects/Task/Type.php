<?php

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Type
{
    private const FEATURE = 'feature';
    private const BUGFIX = 'bugfix';
    private const CODE_REVIEW = 'code review';
    private const QA_TEST = 'qa test';

    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::FEATURE,
            self::BUGFIX,
            self::CODE_REVIEW,
            self::QA_TEST
        ]);

        $this->value = $value;
    }

    public static function feature(): self
    {
        return new self(self::FEATURE);
    }

    public static function bugfix(): self
    {
        return new self(self::BUGFIX);
    }

    public static function codeReview(): self
    {
        return new self(self::CODE_REVIEW);
    }

    public static function qaTest(): self
    {
        return new self(self::QA_TEST);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
