<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Status;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeStatusTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertTrue($task->getStatus()->isEqual(Status::new()));
        $task->changeStatus($status = Status::inWork(), $date = new \DateTimeImmutable());
        $this->assertEquals($status, $task->getStatus());
        $this->assertEquals($date, $task->getStartDate());
        $this->assertNull($task->getEndDate());
        $this->assertTrue($task->getStatus()->isEqual(Status::inWork()));
        $task->changeStatus(Status::done(), $dateEnd = new \DateTimeImmutable('+1 day'));
        $this->assertEquals($date, $task->getStartDate());
        $this->assertEquals($dateEnd, $task->getEndDate());
        $this->assertEquals(100, $task->getProgress());
    }

    public function testFailedTheSame()
    {
        $task = (new TaskBuilder())->build();
        $this->assertTrue($task->getStatus()->isEqual(Status::new()));
        $this->expectExceptionMessage('Status is the same');
        $task->changeStatus(Status::new(), new \DateTimeImmutable());
    }

    public function testWhenTaskGoToDoneImmediatelyStatus()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEquals(0, $task->getProgress());
        $task->changeStatus(Status::done(), $date = new \DateTimeImmutable());
        $this->assertEquals($date, $task->getStartDate());
        $this->assertEquals($date, $task->getEndDate());
        $this->assertEquals(100, $task->getProgress());
    }

    public function testWhenTaskGoFromDoneStatusToAnythingElse()
    {
        $task = (new TaskBuilder())->build();
        $task->changeStatus(Status::done(), $start = new \DateTimeImmutable());
        $task->changeStatus(Status::needApprove(), new \DateTimeImmutable());

        $this->assertEquals(85, $task->getProgress());
        $this->assertEquals($start, $task->getStartDate());
        $this->assertNull($task->getEndDate());
    }
}
