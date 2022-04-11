<?php

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(User::class);
    }

    public function findByConfirmationToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmationToken' => $token]);
    }

    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $email->getValue()])) {
            throw new \DomainException('User is not found');
        }
        return $user;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
                    ->select('COUNT(t.id)')
                    ->innerJoin('t.networks', 'n')
                    ->andWhere('n.network = :network and n.identity = :identity')
                    ->setParameter(':network', $network)
                    ->setParameter(':identity', $identity)
                    ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function get(Id $id): ?User
    {
        if (!$user = $this->repo->find($id->getValue())) {
            throw new \DomainException('User is not found');
        }
        return $user;
    }

    public function find(Id $id): ?User
    {
        return $this->repo->find($id->getValue());
    }
}