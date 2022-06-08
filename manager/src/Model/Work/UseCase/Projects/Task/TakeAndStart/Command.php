<?php

namespace App\Model\Work\UseCase\Projects\Task\TakeAndStart;

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
    public $member;

    public function __construct(int $id, string $member)
    {
        $this->id = $id;
        $this->member = $member;
    }
}
