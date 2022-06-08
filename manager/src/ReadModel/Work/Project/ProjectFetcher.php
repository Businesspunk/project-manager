<?php

namespace App\ReadModel\Work\Project;

use App\ReadModel\Work\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProjectFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $perPage, string $sortBy, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id', 'p.name', 'p.status')
            ->from('work_projects_projects', 'p');

        if ($member = $filter->member) {
            $qb->andWhere('EXISTS(
                SELECT ms.member_id FROM work_projects_memberships ms 
                WHERE ms.project_id = p.id AND ms.member_id = :member
            )');
            $qb->setParameter(':member', $member);
        }

        if ($name = $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(p.name)', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($name) . '%');
        }

        if ($status = $filter->status) {
            $qb->andWhere('p.status = :status');
            $qb->setParameter(':status', $status);
        }

        if (!in_array($sortBy, ['name', 'status', 'sort'], true)) {
            throw new \DomainException('Unable to sort');
        }

        $qb->orderBy($sortBy, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $perPage);
    }

    public function getMaxSort(): int
    {
        $stmt = $this->connection->createQueryBuilder()
                ->select('MAX(sort)')
                ->from('work_projects_projects')
                ->execute();

        return $stmt->fetchOne() ?? 0;
    }

    public function listAll(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('work_projects_projects')
            ->orderBy('sort')
            ->execute();

        return $stmt->fetchAllAssociative();
    }
}
