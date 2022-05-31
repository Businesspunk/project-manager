<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EditTest extends KernelTestCase
{
    public function testSuccess()
    {
        $task = (new TaskBuilder())->build();
        $task->edit($title = 'new title', $content = 'new content');
        $this->assertSame($title, $task->getTitle());
        $this->assertSame($content, $task->getContent());
    }
}
