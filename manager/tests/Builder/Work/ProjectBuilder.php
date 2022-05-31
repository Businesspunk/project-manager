<?php

namespace App\Tests\Builder\Work;

use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Project\Status;

class ProjectBuilder
{
    private $id;
    private $name;
    private $sort;
    private $status;

    public function __construct(Id $id = null, string $name = null, int $sort = null, Status $status = null)
    {
        $this->id = $id ?? Id::next();
        $this->name = $name ?? 'Test project';
        $this->sort = $sort ?? 1;
        $this->status = $status ?? Status::active();
    }

    public function build(): Project
    {
        return new Project(
            $this->id,
            $this->name,
            $this->sort,
            $this->status
        );
    }
}
