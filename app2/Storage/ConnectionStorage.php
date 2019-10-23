<?php

namespace app2\Storage;

use app2\Component\Model;
use app2\Domain\Storage\ConnectionStorageInterface;
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
     * @var Model[][]
     */
    private $connectionRelModels = [];

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
    public function addConnection(TcpConnection $connection, int $userId)
    {
        $this->connectionRelManager[$connection->id] = $userId;

        if (false === array_key_exists($connection->id, $this->connections)) {
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
        unset($this->connections[$connection->id]);
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
     * @return string
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
    public function getUserId(TcpConnection $connection)
    {
        if (array_key_exists($connection->id, $this->connectionRelManager)) {
            return $this->connectionRelManager[$connection->id];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setEditedForms(TcpConnection $connection, Model $model)
    {
        if (false === array_key_exists($connection->id, $this->connectionRelModels)) {
            $this->connectionRelModels[$connection->id] = [];
        }

        $key = $model->getModelName() . '|' . $model->getId();
        if (false === array_key_exists($key, $this->connectionRelModels[$connection->id])) {
            $this->connectionRelModels[$connection->id][$key] = $model;
        }
    }

    /**
     * @inheritDoc
     */
    public function getEditedForms(TcpConnection $connection): array
    {
        if (false === array_key_exists($connection->id, $this->connectionRelModels)) {
            return [];
        }
        return $this->connectionRelModels[$connection->id];
    }
}
