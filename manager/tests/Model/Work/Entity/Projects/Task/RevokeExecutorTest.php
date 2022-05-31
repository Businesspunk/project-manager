<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\MemberBuilder;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RevokeExecutorTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertEmpty($task->getExecutors()->toArray());
        $member = (new MemberBuilder())->build();
        $task->assignExecutor($member);
        $this->assertNotEmpty($task->getExecutors()->toArray());
        $this->assertEquals($member, $task->getExecutors()[0]);
        $task->revokeExecutor($member);
        $this->assertEmpty($task->getExecutors()->toArray());
    }

    public function testFailedDoesNotExist()
    {
        $task = (new TaskBuilder())->build();
        $memberFirst = (new MemberBuilder())->build();
        $memberSecond = (new MemberBuilder())->build();
        $task->assignExecutor($memberFirst);
        $this->expectExceptionMessage('Executor does not exist');
        $task->revokeExecutor($memberSecond);
    }
}
