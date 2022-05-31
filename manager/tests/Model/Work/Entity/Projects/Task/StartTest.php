<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Status;
use App\Tests\Builder\Work\MemberBuilder;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StartTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $member = (new MemberBuilder())->build();
        $task->assignExecutor($member);
        $this->assertNull($task->getStartDate());
        $task->start($date = new \DateTimeImmutable());
        $this->assertEquals($date, $task->getStartDate());
        $this->assertTrue(Status::inWork()->isEqual($task->getStatus()));
    }

    public function testFailedAlreadyStarted()
    {
        $task = (new TaskBuilder())->build();
        $member = (new MemberBuilder())->build();
        $task->assignExecutor($member);
        $task->start(new \DateTimeImmutable());
        $this->expectExceptionMessage('Task is already started');
        $task->start(new \DateTimeImmutable());
    }

    public function testFailedTaskHasNoExecutors()
    {
        $task = (new TaskBuilder())->build();
        $this->expectExceptionMessage('Task has no executors');
        $task->start(new \DateTimeImmutable());
    }
}
