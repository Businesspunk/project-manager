<?php

namespace App\Model\User\Entity\User;

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

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last;
    }

    public function __toString()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }
}
