<?php

namespace App\ReadModel\Work\Group;

use Doctrine\DBAL\Connection;

class GroupFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('g.id', 'g.name', 'COUNT(m.id) as members')
            ->from('work_members_groups', 'g')
            ->leftJoin('g', 'work_members_members', 'm', 'g.id = m.group_id')
            ->groupBy('g.id')
            ->execute();

        $result = $stmt->fetchAllAssociative();
        return $result ?: null;
    }

    public function assoc(): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('work_members_groups')
            ->orderBy('name')
            ->execute();

        $result = $stmt->fetchAllAssociative();
        return $result ? array_column($result, 'name', 'id') : null;
    }
}