<?php

namespace App\Tests\Model\Work\Entity\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Id;
use App\Tests\Builder\Work\TaskBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SetChildOfTest extends KernelTestCase
{
    public function testSuccess()
    {
        $parent = (new TaskBuilder(new Id(1)))->build();
        $child = (new TaskBuilder(new Id(2)))->build();
        $this->assertNull($child->getParent());
        $child->setChildOf($parent);
        $this->assertSame($parent, $child->getParent());
    }

    public function testFailedTheSameParent()
    {
        $parent = (new TaskBuilder())->build();
        $this->expectExceptionMessage('Cycle hierarchy');
        $parent->setChildOf($parent);
    }

    public function testFailedCycleHierarchy()
    {
        $grandParent = (new TaskBuilder(new Id(1)))->build();
        $parent = (new TaskBuilder(new Id(2)))->build();
        $child = (new TaskBuilder(new Id(3)))->build();

        $parent->setChildOf($grandParent);
        $child->setChildOf($parent);

        $this->expectExceptionMessage('Cycle hierarchy');
        $grandParent->setChildOf($child);
    }
}
