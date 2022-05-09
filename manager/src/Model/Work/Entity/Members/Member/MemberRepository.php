<?php

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class MemberRepository
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Member::class);
    }

    public function add(Member $member): void
    {
        $this->em->persist($member);
    }

    public function remove(Member $member): void
    {
        $this->em->remove($member);
    }

    public function find(Id $id): ?Member
    {
        return $this->repo->find($id->getValue());
    }

    public function hasById(Id $id): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.id = :id')
                ->setParameter(':id', $id)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.email)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function get(Id $id): ?Member
    {
        if (!$group = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Group is not exist');
        }
        return $group;
    }
}