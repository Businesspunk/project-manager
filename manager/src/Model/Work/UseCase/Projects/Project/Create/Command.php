<?php

namespace App\Model\Work\UseCase\Projects\Project\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $name;
    /**
     * @var integer
     * @Assert\NotBlank
     */
    public $sort;
}
