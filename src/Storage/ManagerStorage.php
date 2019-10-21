<?php

namespace app\Storage;

use app\Domain\Storage\ConnectionStorageInterface;
use app\Domain\Storage\ManagerStorageInterface;
use Workerman\Connection\TcpConnection;

/**
 * Class ManagerStorage
 *
 * @package app\Storage
 */
class ManagerStorage implements ManagerStorageInterface
{
    private $managers;

    /**
     * Двумерный массив. Первый ключ - идентификатор менеджера, второй ключ - идентификатор соединения
     *
     * @var TcpConnection[][]
     */
    private $managerRelConnections;

    /**
     * @var string[]
     */
    private $statuses;

    /**
     * @inheritDoc
     */
    public function addManagerConnection(int $managerId, TcpConnection $connection)
    {
        if (!isset($this->managerRelConnections[$managerId])) {
            $this->managerRelConnections[$managerId] = [];
        }
        if (false === array_search($connection->id, $this->managerRelConnections[$managerId], true)) {
            $this->managerRelConnections[$managerId][$connection->id] = $connection;
            $this->statuses[$managerId] = ConnectionStorageInterface::STATUS_ONLINE;
        }
    }

    /**
     * @inheritDoc
     */
    public function getConnectionsByManagerId(int $managerId): array
    {
        return $this->managerRelConnections[$managerId];
    }

    /**
     * @inheritDoc
     */
    public function getManagerIdByConnection(TcpConnection $connection): int
    {
        foreach ($this->managerRelConnections as $managerId => $connections) {
            foreach ($connections as $item) {
                if ($item->id === $connection->id) {
                    return $managerId;
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getStatus(int $managerId)
    {
        return $this->statuses[$managerId];
    }

    /**
     * @param int    $managerId
     * @param string $status
     */
    public function setStatus(int $managerId, string $status)
    {
        $this->statuses[$managerId] = $status;
    }
}
