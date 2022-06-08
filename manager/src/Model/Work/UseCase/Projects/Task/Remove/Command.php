<?php

namespace App\Model\Work\UseCase\Projects\Task\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
