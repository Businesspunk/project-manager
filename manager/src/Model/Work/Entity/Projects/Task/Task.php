<?php

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("work_projects_tasks", indexes={
 *  @ORM\Index(columns={"date"})
 * })
 */
class Task
{
    /**
     * @var Id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="work_projects_tasks_seq", initialValue=1)
     * @ORM\Column(type="work_projects_task_id")
     */
    private $id;
    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;
    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity=Member::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="datetime_immutable")
     */
    private $date;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="date_immutable", nullable="true")
     */
    private $planDate;
    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private $title;
    /**
     * @var string
     * @ORM\Column (type="text", nullable="true")
     */
    private $content;
    /**
     * @var Type
     * @ORM\Column (type="work_projects_task_type")
     */
    private $type;
    /**
     * @var int
     * @ORM\Column (type="smallint")
     */
    private $priority;
    /**
     * @var int
     * @ORM\Column (type="smallint")
     */
    private $progress;
    /**
     * @var Status
     * @ORM\Column (type="work_projects_task_status")
     */
    private $status;
    /**
     * @var Task
     * @ORM\ManyToOne (targetEntity="Task")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $parent;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="datetime_immutable", nullable="true")
     */
    private $startDate;
    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="date_immutable", nullable="true")
     */
    private $endDate;
    /**
     * @var ArrayCollection|Member[]
     * @ORM\ManyToMany (targetEntity=Member::class)
     * @ORM\JoinTable (name="work_projects_tasks_members")
     */
    private $executors;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        \DateTimeImmutable $date,
        string $title,
        ?string $content,
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

    public function getExecutors(): Collection
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

    public function getContent(): ?string
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

    public function hasExecutorById(string $id)
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual(new MemberId($id))) {
                return true;
            }
        }
        return false;
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

    public function plan(?\DateTimeImmutable $date): void
    {
        $this->planDate = $date;
    }

    public function setChildOf(?self $parent): void
    {
        if ($parent === null) {
            $this->parent = $parent;
            return;
        }

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

    public function hasExecutor(Member $member): bool
    {
        /** @var Member $executor */
        foreach ($this->executors as $executor) {
            if ($executor->isEqual($member)) {
                return true;
            }
        }
        return false;
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
