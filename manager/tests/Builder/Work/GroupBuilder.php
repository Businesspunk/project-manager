<?php

namespace App\Tests\Builder\Work;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\Id;

class GroupBuilder
{
    private $id;
    private $name;

    public function __construct(Id $id = null, string $name = null)
    {
        $this->id = $id ?? Id::next();
        $this->name = $name ?? 'Test group';
    }

    public function build(): Group
    {
        return new Group(
            $this->id,
            $this->name
        );
    }
}
