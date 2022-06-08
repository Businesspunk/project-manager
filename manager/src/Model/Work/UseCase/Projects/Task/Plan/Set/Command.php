<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var \DateTimeImmutable
     * @Assert\NotBlank
     * @Assert\Date
     */
    public $date;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
