<?php

namespace App\Model\User\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $email)
    {
        Assert::notEmpty($email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           throw new Exception('Not valid email');
        }

        $this->value = mb_strtolower($email);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}