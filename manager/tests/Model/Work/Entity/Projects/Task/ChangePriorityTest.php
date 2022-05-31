<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webmozart\Assert\InvalidArgumentException;

class ChangePriorityTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertSame(2, $task->getPriority());
        $task->changePriority(3);
        $this->assertSame(3, $task->getPriority());
    }

    public function testFailedTheSame()
    {
        $task = (new TaskBuilder())->build();
        $this->assertSame(2, $task->getPriority());
        $this->expectExceptionMessage('Priority is the same');
        $task->changePriority(2);
    }

    public function testFailedOutOfRange1()
    {
        $task = (new TaskBuilder())->build();
        $this->assertSame(2, $task->getPriority());
        $this->expectException(InvalidArgumentException::class);
        $task->changePriority(0);
    }

    public function testFailedOutOfRange2()
    {
        $task = (new TaskBuilder())->build();
        $this->assertSame(2, $task->getPriority());
        $this->expectException(InvalidArgumentException::class);
        $task->changePriority(5);
    }
}
