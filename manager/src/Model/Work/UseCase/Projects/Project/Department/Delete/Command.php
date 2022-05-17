<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Delete;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $projectId;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $departmentId;

    public function __construct(string $projectId, string $departmentId)
    {
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
    }
}
