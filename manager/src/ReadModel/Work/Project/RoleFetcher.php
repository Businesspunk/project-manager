<?php

namespace App\ReadModel\Work\Project;

use Doctrine\DBAL\Connection;

class RoleFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('r.id', 'r.name', 'r.permissions', 'COUNT(mr.role_id) as role_count')
            ->from('work_projects_roles', 'r')
            ->leftJoin('r', 'work_projects_memberships_roles', 'mr', 'r.id = mr.role_id')
            ->groupBy('r.id')
            ->orderBy('r.name')
            ->execute();

        $rows = $stmt->fetchAll();

        return array_map(static function ($row) {
            $row['permissions'] = $row['permissions'] ? json_decode($row['permissions'], true) : [];
            return $row;
        }, $rows);
    }
}
