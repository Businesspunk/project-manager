<?php

namespace App\Model\User\UseCase\Block;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}