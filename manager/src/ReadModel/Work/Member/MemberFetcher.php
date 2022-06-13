<?php

namespace App\ReadModel\Work\Member;

use App\Model\Work\Entity\Members\Member\Status;
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
                'm.status',
                '(SELECT COUNT(*) FROM work_projects_memberships ms WHERE ms.member_id = m.id) as projects'
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

    public function listWithGroups(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) as name',
                'g.name as group'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_members_groups', 'g', 'm.group_id = g.id')
            ->orderBy('g.name')
            ->execute();

        return $stmt->fetchAllAssociative();
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

    public function activeDepartmentListForProject(string $project): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('m.id', 'CONCAT(m.name_first, " ", m.name_last) as name', 'm.email', 'd.name as department')
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_projects_memberships', 'mb', 'm.id = mb.member_id')
            ->innerJoin('mb', 'work_projects_memberships_departments', 'md', 'mb.id = md.membership_id')
            ->innerJoin('md', 'work_projects_departments', 'd', 'md.department_id = d.id')
            ->where('m.status = :status and mb.project_id = :project')
            ->orderBy('d.name')->addOrderBy('name')
            ->setParameters([':status' => Status::STATUS_ACTIVE, ':project' => $project])
            ->execute();

        $result = $stmt->fetchAllAssociative();
        return $result ?: null;
    }

    public function activeDepartmentList(string $project = null): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('m.id', 'CONCAT(m.name_first, \' \', m.name_last) as name', 'm.email', 'd.name as department')
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_projects_memberships', 'mb', 'm.id = mb.member_id')
            ->innerJoin('mb', 'work_projects_memberships_departments', 'md', 'mb.id = md.membership_id')
            ->innerJoin('md', 'work_projects_departments', 'd', 'md.department_id = d.id')
            ->where('m.status = :status')
            ->orderBy('d.name')->addOrderBy('name')
            ->setParameters([':status' => Status::STATUS_ACTIVE]);

        if ($project) {
            $stmt = $stmt
                ->andWhere('mb.project_id = :project')
                ->setParameters([':project' => $project]);
        }

        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();
        return $result ?: null;
    }
}
