<?php

namespace app2\Storage;

use app2\Domain\Storage\ConnectionStorageInterface;
use app2\Domain\Storage\ManagerStorageInterface;
use app2\Domain\Storage\ModelStorageInterface;
use app2\Domain\Storage\StorageInterface;

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
}
