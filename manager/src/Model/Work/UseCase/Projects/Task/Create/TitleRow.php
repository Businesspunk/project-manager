<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use Symfony\Component\Validator\Constraints as Assert;

class TitleRow
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $value;

    public function __construct(string $id)
    {
        $this->value = $id;
    }
}
