<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

class Command
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $group;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}