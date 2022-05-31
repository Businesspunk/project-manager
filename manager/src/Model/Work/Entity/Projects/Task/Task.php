<?php

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

class Task
{
    private $id;
    private $project;
    private $author;
    private $date;
    private $planDate;
    private $title;
    private $content;
    private $type;
    private $priority;
    private $progress;
    private $status;
    private $parent;
    private $startDate;
    private $endDate;
    private $executors;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        \DateTimeImmutable $date,
        string $title,
        string $content,
        int $priority,
        Type $type
    ) {
        $this->guardPriority($priority);

        $this->id = $id;
        $this->project = $project;
        $this->author = $author;
        $this->date = $date;
        $this->title = $title;
        $this->content = $content;
        $this->priority = $priority;
        $this->type = $type;

        $this->status = Status::new();
        $this->executors = new ArrayCollection();
        $this->progress = 0;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getExecutors(): ArrayCollection
    {
        return $this->executors;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getPlanDate(): ?\DateTimeImmutable
    {
        return $this->planDate;
    }

    public function isDone(): bool
    {
        return $this->status->isDone();
    }

    public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function isEqual(self $other): bool
    {
        return $this->id->isEqual($other->getId());
    }

    public function start(\DateTimeImmutable $date)
    {
        if ($this->startDate) {
            throw new \DomainException('Task is already started');
        }

        if (!$this->executors->count()) {
            throw new \DomainException('Task has no executors');
        }

        $this->changeStatus(Status::inWork(), $date);
    }

    public function edit(string $title, string $content): void
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function plan(\DateTimeImmutable $date): void
    {
        $this->planDate = $date;
    }

    public function setChildOf(self $parent): void
    {
        if ($this->isEqual($parent)) {
            throw new \DomainException('Cycle hierarchy');
        }

        $current = $parent->getParent();
        while ($current) {
            if ($this->isEqual($current)) {
                throw new \DomainException('Cycle hierarchy');
            }
            $current = $current->getParent();
        }
        $this->parent = $parent;
    }

    public function move(Project $project): void
    {
        if ($this->project->isEqual($project)) {
            throw new \DomainException('Project is the same');
        }
        $this->project = $project;
    }

    public function changeType(Type $type): void
    {
        if ($this->type->isEqual($type)) {
            throw new \DomainException('Type is the same');
        }
        $this->type = $type;
    }

    public function changeStatus(Status $status, \DateTimeImmutable $date): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is the same');
        }

        if ($this->isDone() && !$status->isDone() && $this->progress === 100) {
            $this->changeProgress(85);
        }

        $this->status = $status;

        if (!$this->isNew() && !$this->startDate) {
            $this->startDate = $date;
        }

        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress(100);
            }
            $this->endDate = $date;
        } else {
            $this->endDate = null;
        }
    }

    public function changeProgress(int $progress): void
    {
        Assert::range($progress, 0, 100);
        if ($this->progress === $progress) {
            throw new \DomainException('Progress is the same');
        }
        $this->progress = $progress;
    }

    public function changePriority(int $priority): void
    {
        $this->guardPriority($priority);
        if ($this->priority === $priority) {
            throw new \DomainException('Priority is the same');
        }
        $this->priority = $priority;
    }

    public function assignExecutor(Member $member): void
    {
        /** @var Member $executor */
        foreach ($this->executors as $executor) {
            if ($executor->isEqual($member)) {
                throw new \DomainException('Executor is already attached');
            }
        }
        $this->executors->add($member);
    }

    public function revokeExecutor(Member $member): void
    {
        /** @var Member $executor */
        foreach ($this->executors as $executor) {
            if ($executor->isEqual($member)) {
                $this->executors->removeElement($executor);
                return;
            }
        }
        throw new \DomainException('Executor does not exist');
    }

    private function guardPriority(int $priority): void
    {
        Assert::range($priority, 1, 4);
    }
}
