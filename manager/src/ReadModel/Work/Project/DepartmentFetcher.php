<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Project;

use Doctrine\DBAL\Connection;

class DepartmentFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allOfProject(string $project): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'd.id',
                'd.name',
                '(
                    SELECT COUNT(ms.member_id)
                    FROM work_projects_memberships ms
                    INNER JOIN work_projects_memberships_departments md ON ms.id = md.membership_id
                    WHERE md.department_id = d.id AND ms.project_id = :project
                ) AS members_count'
            )
            ->from('work_projects_departments', 'd')
            ->andWhere('project_id = :project')
            ->setParameter(':project', $project)
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAllAssociative();
    }

    public function assoc(string $project): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'd.id',
                'd.name'
            )
            ->from('work_projects_departments', 'd')
            ->andWhere('project_id = :project')
            ->setParameter(':project', $project)
            ->orderBy('name')
            ->execute();

        $result = $stmt->fetchAllAssociative();
        return $result ? array_column($result, 'name', 'id') : null;
    }
}
