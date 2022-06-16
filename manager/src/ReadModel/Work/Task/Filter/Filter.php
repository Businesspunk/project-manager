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
    public $project;
    public $author;

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

    public function forExecutor(string $id): self
    {
        $this->executor = $id;
        return $this;
    }

    public function forAuthor(string $id): self
    {
        $this->author = $id;
        return $this;
    }
}
