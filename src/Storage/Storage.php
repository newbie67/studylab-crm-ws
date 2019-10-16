<?php

namespace app\Storage;

use app\Domain\Storage\ConnectionStorageInterface;
use app\Domain\Storage\ManagerStorageInterface;
use app\Domain\Storage\StorageInterface;

/**
 * Class Storage
 *
 * @package app\Storage
 */
class Storage implements StorageInterface
{
    /**
     * @var ConnectionStorageInterface
     */
    private $connectionStorage;

    /**
     * @var ManagerStorageInterface
     */
    private $managerStorage;

    /**
     * @inheritDoc
     */
    public function getConnectionStorage(): ConnectionStorageInterface
    {
        if (null === $this->connectionStorage) {
            $this->connectionStorage = new ConnectionStorage();
        }
        return $this->connectionStorage;
    }

    /**
     * @inheritDoc
     */
    public function getManagerStorage(): ManagerStorageInterface
    {
        if (null === $this->managerStorage) {
            $this->managerStorage = new ManagerStorage();
        }
        return $this->managerStorage;
    }




//    /**
//     * @var string[][]
//     */
//    private $managerRelConnection = [];
//
//    /**
//     * @var TcpConnection[]
//     */
//    private $connections = [];
//
//    /**
//     * @inheritDoc
//     */
//    public function addConnection(TcpConnection $connection)
//    {
//        if (false === array_key_exists($connection->id, $this->connections)) {
//            $this->connections[$connection->id] = $connection;
//        }
//    }
//
//    /**
//     * @inheritDoc
//     */
//    public function addConnectionToManager(int $connectionId, int $managerId)
//    {
//        if (!isset($this->managerRelConnection[$managerId])) {
//            $this->managerRelConnection[$managerId] = [];
//        }
//        if (in_array($connectionId, $this->managerRelConnection[$managerId], true)) {
//            $this->managerRelConnection[$managerId][] = $connectionId;
//        }
//    }
}
