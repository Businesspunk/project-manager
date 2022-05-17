<?php

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

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
    public $name;

    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }
}
