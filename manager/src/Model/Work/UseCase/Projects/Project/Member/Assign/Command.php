<?php

namespace App\Model\Work\UseCase\Projects\Project\Member\Assign;

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
    /**
     * @var array
     * @Assert\NotBlank
     * @Assert\Count(min=1)
     */
    public $departments;
    /**
     * @var array
     * @Assert\NotBlank
     * @Assert\Count(min=1)
     */
    public $roles;

    public function __construct(string $project)
    {
        $this->project = $project;
    }
}
