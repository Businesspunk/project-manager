<?php

namespace App\ReadModel\Work\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;

class Filter
{
    public $name;
    public $status = Status::STATUS_ACTIVE;
    public $member;

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
