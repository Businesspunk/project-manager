<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webmozart\Assert\InvalidArgumentException;

class ChangeProgressTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEquals(0, $task->getProgress());
        $task->changeProgress(10);
        $this->assertEquals(10, $task->getProgress());
    }

    public function testFailedTheSame()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEquals(0, $task->getProgress());
        $this->expectExceptionMessage('Progress is the same');
        $task->changeProgress(0);
    }

    public function testFailedNegativeDigits()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEquals(0, $task->getProgress());
        $this->expectException(InvalidArgumentException::class);
        $task->changeProgress(-10);
    }
}
