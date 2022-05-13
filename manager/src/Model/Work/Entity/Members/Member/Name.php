<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private $first;

    /**
     * @var string
     * @ORM\Column (type="string")
     */
    private $last;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);
        $this->first = $first;
        $this->last = $last;
    }

    public function getFirstName(): string
    {
        return $this->first;
    }

    public function getLastName(): string
    {
        return $this->last;
    }

    public function __toString()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }
}
