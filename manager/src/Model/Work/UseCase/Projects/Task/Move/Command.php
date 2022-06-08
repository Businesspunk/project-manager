<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $project;
    /**
     * @var bool
     * @Assert\NotBlank
     * @Assert\Type("bool")
     */
    public $withChildren;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
