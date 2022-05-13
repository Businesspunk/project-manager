<?php

namespace App\ReadModel\Network;

use Doctrine\DBAL\Connection;

class NetworkFetch
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function existsByNetworkAndIdentity(string $network, string $identity)
    {
        return $this->connection->createQueryBuilder('t')
                ->select('COUNT(*)')
                ->from('user_user_networks')
                ->where('network = :network AND identity = :identity')
                ->setParameters([
                    ':network' => $network,
                    ':identity' => $identity
                ])
                ->execute()->fetchColumn(0) > 0;
    }
}
