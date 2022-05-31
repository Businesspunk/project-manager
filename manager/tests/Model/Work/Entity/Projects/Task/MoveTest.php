<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\ProjectBuilder;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MoveTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $project = (new ProjectBuilder())->build();
        $task->move($project);
        $this->assertEquals($project, $task->getProject());
    }

    public function testFailedTheSame()
    {
        $task = (new TaskBuilder())->build();
        $project = (new ProjectBuilder())->build();
        $task->move($project);
        $this->expectExceptionMessage('Project is the same');
        $task->move($project);
    }
}
