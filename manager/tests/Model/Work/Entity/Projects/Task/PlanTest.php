<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlanTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $this->assertNull($task->getPlanDate());
        $task->plan($date = new \DateTimeImmutable('+1 day'));
        $this->assertSame($date, $task->getPlanDate());
    }
}
