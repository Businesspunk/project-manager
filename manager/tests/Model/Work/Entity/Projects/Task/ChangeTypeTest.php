<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Type;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangeTypeTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertTrue($task->getType()->isEqual(Type::feature()));
        $task->changeType($type = Type::bugfix());
        $this->assertEquals($type, $task->getType());
        $this->assertTrue($task->getType()->isEqual(Type::bugfix()));
    }

    public function testFailedTheSameType()
    {
        $task = (new TaskBuilder())->build();
        $this->assertTrue($task->getType()->isEqual(Type::feature()));
        $this->expectExceptionMessage('Type is the same');
        $task->changeType(Type::feature());
    }
}
