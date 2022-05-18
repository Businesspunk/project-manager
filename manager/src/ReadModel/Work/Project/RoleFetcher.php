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
            ->select('id', 'name', 'permissions')
            ->from('work_projects_roles')
            ->execute();

        return $stmt->fetchAll();
    }
}
