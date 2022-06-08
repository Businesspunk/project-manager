<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var array
     * @Assert\NotBlank
     * @Assert\Count (min={1})
     */
    public $members;

    public function __construct(int $id, array $members)
    {
        $this->id = $id;
    }
}
