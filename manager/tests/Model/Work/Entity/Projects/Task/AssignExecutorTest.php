<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\MemberBuilder;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssignExecutorTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEmpty($task->getExecutors()->toArray());
        $member = (new MemberBuilder())->build();
        $task->assignExecutor($member);
        $this->assertNotEmpty($task->getExecutors()->toArray());
        $this->assertEquals($member, $task->getExecutors()[0]);
    }

    public function testFailedAlreadyAttached()
    {
        $task = (new TaskBuilder())->build();
        $member = (new MemberBuilder())->build();
        $task->assignExecutor($member);
        $this->expectExceptionMessage('Executor is already attached');
        $task->assignExecutor(clone $member);
    }
}
