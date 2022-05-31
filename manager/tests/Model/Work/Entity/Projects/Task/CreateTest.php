<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Tests\Builder\Work\MemberBuilder;
use App\Tests\Builder\Work\ProjectBuilder;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTest extends KernelTestCase
{
    public function testSuccess(): void
    {
        $project = (new ProjectBuilder())->build();
        $author = (new MemberBuilder())->build();

        $task = (new TaskBuilder(
            $id = new Id(1),
            $project,
            $author,
            $date = new \DateTimeImmutable(),
            $title = 'Project title',
            $content = 'Project content',
            $priority = 4,
            $type = Type::feature()
        ))->build();

        $this->assertSame($id, $task->getId());
        $this->assertSame($project, $task->getProject());
        $this->assertSame($author, $task->getAuthor());
        $this->assertSame($date, $task->getDate());
        $this->assertSame($title, $task->getTitle());
        $this->assertSame($content, $task->getContent());
        $this->assertSame($priority, $task->getPriority());
        $this->assertSame($type, $task->getType());
        $this->assertTrue($task->getStatus()->isEqual(Status::new()));
        $this->assertSame(0, $task->getProgress());
        $this->assertNull($task->getPlanDate());
        $this->assertNull($task->getParent());
        $this->assertNull($task->getStartDate());
        $this->assertNull($task->getEndDate());
        $this->assertEmpty($task->getExecutors()->toArray());
    }
}
