<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetToken;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    /**
     * @var \DateInterval
     */
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}