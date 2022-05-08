<?php

namespace App\Model\Work\Entity\Members\Member;

use Webmozart\Assert\Assert;

class Email
{
    private $value;

    public function __construct(string $email)
    {
        Assert::notEmpty($email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Not valid email');
        }

        $this->value = mb_strtolower($email);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}