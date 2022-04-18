<?php

namespace App\ReadModel\User;

class DetailView
{
    public $id;
    public $date;
    public $role;
    public $status;

    /**
     * @var NetworkView[]|null
     */
    public $networks;
}