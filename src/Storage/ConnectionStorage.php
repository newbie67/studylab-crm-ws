<?php

namespace app\Storage;

use app\Domain\Storage\ConnectionStorageInterface;
use Workerman\Connection\TcpConnection;

/**
 * Class ConnectionStorage
 *
 * @package app\Storage
 */
class ConnectionStorage implements ConnectionStorageInterface
{
    /**
     * Ключом выступает идентификатор коннекта
     *
     * @var TcpConnection[]
     */
    private $connections = [];

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->connections;
    }

    /**
     * @inheritDoc
     */
    public function addConnection(TcpConnection $connection)
    {
        $this->connections[$connection->id] = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getAllWithout(int $id)
    {
        $tmp = $this->connections;
        unset($tmp[$id]);

        return $tmp;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        if (array_key_exists($id, $this->connections)) {
            return $this->connections[$id];
        }
        return null;
    }
}
