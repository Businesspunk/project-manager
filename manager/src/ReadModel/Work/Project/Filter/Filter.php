<?php

namespace App\ReadModel\Work\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;

class Filter
{
    public $name;
    public $status = Status::STATUS_ACTIVE;
}
