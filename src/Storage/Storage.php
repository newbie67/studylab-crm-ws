<?php

namespace app\Storage;

use app\Domain\Storage\ConnectionStorageInterface;
use app\Domain\Storage\ManagerStorageInterface;
use app\Domain\Storage\ModelStorageInterface;
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
}
