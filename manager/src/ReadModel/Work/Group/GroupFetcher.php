<?php

namespace App\ReadModel\Work\Group;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

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
            ->select('id', 'name')
            ->from('work_members_groups')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, GroupView::class);
        $result = $stmt->fetchAll();
        return $result ?: null;
    }
}