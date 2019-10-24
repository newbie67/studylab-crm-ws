<?php

namespace App\Storage;

use App\Domain\Model\ConnectionInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use App\Model\Connection;
use Workerman\Connection\TcpConnection;

class ConnectionStorage implements ConnectionStorageInterface
{
    /**
     * @var ConnectionInterface[]
     */
    private $connections = [];

    /**
     * @inheritDoc
     */
    public function addConnection(TcpConnection $connection): ConnectionInterface
    {
        if (false === array_key_exists($connection->id, $this->connections)) {
            $this->connections[$connection->id] = Connection::getInstance($connection);
        }
        return $this->connections[$connection->id];
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
        $returned = [];
        foreach ($this->connections as $otherConnection) {
            if ($otherConnection->getTcpConnection()->id !== $connection->id) {
                $returned[] = $otherConnection;
            }
        }

        return $returned;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->connections;
    }








//    public function findOne(TcpConnection $connection): ConnectionInterface
//    {
//        // TODO: Implement findOne() method.
//    }
//
//
//
//
//    public function removeConnection(TcpConnection $connection)
//    {
//        // TODO: Implement removeConnection() method.
//    }
}