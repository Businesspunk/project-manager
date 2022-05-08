<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\EntityManagerInterface;

class MemberRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Member::class);
    }
}