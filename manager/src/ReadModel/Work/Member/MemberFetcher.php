<?php

namespace App\ReadModel\Work\Member;

use App\ReadModel\Work\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    private $connection;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $perPage, string $sortBy, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'm.email',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) as name',
                'g.name as group',
                'g.id as group_id',
                'm.status'
            )
            ->from('work_members_members', 'm')
            ->leftJoin('m', 'work_members_groups', 'g', 'g.id = m.group_id');

        if ($name = $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(TRIM(CONCAT(name_first, \' \', name_last)))', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($name) . '%');
        }

        if ($email = $filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter(':email', '%' . mb_strtolower($email) . '%');
        }

        if ($group = $filter->group) {
            $qb->andWhere('group_id = :group');
            $qb->setParameter(':group', $group);
        }

        if ($status = $filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $status);
        }

        if (!in_array($sortBy, ['name', 'email', 'group_id', 'status'], true)) {
            throw new \DomainException('Unable to sort');
        }

        $qb->orderBy($sortBy, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    public function find(string $id): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('work_members_members')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $result = $stmt->fetchAssociative();
        return $result ?: null;
    }
}
