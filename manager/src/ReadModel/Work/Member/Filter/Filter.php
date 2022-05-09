<?php

namespace App\ReadModel\Work\Member\Filter;

use App\Model\Work\Entity\Members\Member\Status;

class Filter
{
    public $name;
    public $email;
    public $group;
    public $status = Status::STATUS_ACTIVE;
}