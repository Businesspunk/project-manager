<?php

namespace App\ReadModel\Work\Task\Filter;

class Filter
{
    public $search;
    public $member;
    public $type;
    public $status;
    public $priority;
    public $executor;

    public function __construct(?string $member)
    {
        $this->member = $member;
    }

    public static function all(): self
    {
        return new self(null);
    }

    public static function forMember(string $id): self
    {
        return new self($id);
    }
}
