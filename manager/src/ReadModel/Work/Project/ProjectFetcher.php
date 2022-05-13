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
            ->select('id', 'name', 'status')
            ->from('work_projects_projects');

        if ($name = $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(name)', ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($name) . '%');
        }

        if ($status = $filter->status) {
            $qb->andWhere('status = :status');
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

        return $stmt->fetchOne();
    }
}
