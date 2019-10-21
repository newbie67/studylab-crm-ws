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
     * Содержит в себе время старта коннекшена, ключом выступает connection->id
     *
     * @var int[]
     */
    private $connectionsTimeStart = [];

    /**
     * Хранит в себе статус соединения
     *
     * @var int[]
     */
    private $connectionStatuses = [];

    /**
     * @var int[]
     */
    private $connectionRelManager = [];

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return $this->connections;
    }

    /**
     * @inheritDoc
     */
    public function addConnection(TcpConnection $connection, int $managerId)
    {
        $this->connectionRelManager[$connection->id] = $managerId;

        if (false === isset($this->connections[$connection->id])) {
            $this->connections[$connection->id] = $connection;
            $this->connectionsTimeStart[$connection->id] = time();
            $this->setConnectionStatus($connection, ConnectionStorageInterface::STATUS_ONLINE);
        }
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(TcpConnection $connection)
    {
        unset($this->connectionRelManager[$connection->id]);
        unset($this->connectionsTimeStart[$connection->id]);
        unset($this->connectionStatuses[$connection->id]);
    }

    /**
     * @inheritDoc
     */
    public function getAllWithout(int $id): array
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

    /**
     * @inheritDoc
     */
    public function getTimeStart(TcpConnection $connection): int
    {
        return $this->connectionsTimeStart[$connection->id];
    }

    /**
     * @param TcpConnection $connection
     *
     * @return int
     */
    public function getStatus(TcpConnection $connection): string
    {
        return $this->connectionStatuses[$connection->id];
    }

    /**
     * @inheritDoc
     */
    public function setConnectionStatus(TcpConnection $connection, string $status)
    {
        $this->connectionStatuses[$connection->id] = $status;
    }

    /**
     * @inheritDoc
     */
    public function getManagerId(TcpConnection $connection): int
    {
        if (array_key_exists($connection->id, $this->connectionRelManager)) {
            return $this->connectionRelManager[$connection->id];
        }
        return null;
    }
}
