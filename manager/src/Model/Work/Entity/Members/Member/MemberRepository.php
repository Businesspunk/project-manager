<?php

namespace App\Model\Work\Entity\Members\Member;

use App\Model\Work\Entity\Members\Group\Group;
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

    public function has(Id $id): bool
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

    public function hasByGroup(Group $group): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.group)')
                ->andWhere('t.group = :group')
                ->setParameter(':group', $group)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function get(Id $id): ?Member
    {
        if (!$group = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Group is not exist');
        }
        return $group;
    }

    public function getByIds(array $ids): ?array
    {
        return $this->repo->findBy(array('id' => $ids));
    }
}
