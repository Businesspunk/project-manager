<?php

namespace App\Model\Work\UseCase\Projects\Project\Member\Delete;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $project;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $member;

    public function __construct(string $project, string $member)
    {
        $this->project = $project;
        $this->member = $member;
    }
}
