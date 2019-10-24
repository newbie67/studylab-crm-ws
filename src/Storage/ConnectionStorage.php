<?php

namespace App\Storage;

use App\Domain\Storage\ConnectionStorageInterface;
use Workerman\Connection\TcpConnection;

class ConnectionStorage implements ConnectionStorageInterface
{
    /**
     * @var TcpConnection[]
     */
    private $connections = [];

    /**
     * @inheritDoc
     */
    public function addConnection(TcpConnection $connection)
    {
        if (false === array_key_exists($connection->id, $this->connections)) {
            $this->connections[$connection->id] = $connection;
        }
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(TcpConnection $connection)
    {
        unset($this->connections[$connection->id]);
    }

    /**
     * @inheritDoc
     */
    public function findAllWithout(TcpConnection $connection): array
    {
        $tmp = $this->connections;
        unset($tmp[$connection->id]);
        return $tmp;
    }
}
