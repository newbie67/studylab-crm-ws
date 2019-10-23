<?php

namespace App\Domain\Storage;

use App\Domain\Model\ConnectionInterface;

interface ConnectionStorageInterface
{
    /**
     * @param ConnectionInterface $connection
     *
     * @return mixed
     */
    public function addConnection(ConnectionInterface $connection);

    /**
     * @param ConnectionInterface $connection
     *
     * @return mixed
     */
    public function removeConnection(ConnectionInterface $connection);

    /**
     * @return ConnectionInterface[]
     */
    public function getAll(): array;

    /**
     * @param ConnectionInterface $connection
     *
     * @return ConnectionInterface[]
     */
    public function getAllWithout(ConnectionInterface $connection): array;
}