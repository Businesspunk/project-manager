<?php

namespace App\Tests\Builder\Work;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\Type;

class TaskBuilder
{
    private $id;
    private $project;
    private $author;
    private $date;
    private $title;
    private $content;
    private $priority;
    private $type;

    public function __construct(
        Id $id = null,
        Project $project = null,
        Member $author = null,
        \DateTimeImmutable $date = null,
        string $title = null,
        string $content = null,
        int $priority = null,
        Type $type = null
    ) {
        $this->id = $id ?? new Id(1);
        $this->project = $project ?? (new ProjectBuilder())->build();
        $this->author = $author ?? (new MemberBuilder())->build();
        $this->date = $date ?? new \DateTimeImmutable();
        $this->title = $title ?? 'Project title';
        $this->content = $content ?? 'Project content';
        $this->priority = $priority ?? 2;
        $this->type = $type ?? Type::feature();
    }

    public function build(): Task
    {
        return new Task(
            $this->id,
            $this->project,
            $this->author,
            $this->date,
            $this->title,
            $this->content,
            $this->priority,
            $this->type
        );
    }
}
