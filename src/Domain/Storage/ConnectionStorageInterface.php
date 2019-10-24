<?php

namespace App\Domain\Storage;

use App\Domain\Model\ConnectionInterface;
use Workerman\Connection\TcpConnection;

interface ConnectionStorageInterface
{
    /**
     * @param TcpConnection $connection
     *
     * @return mixed
     */
    public function addConnection(TcpConnection $connection);

    /**
     * @param TcpConnection $connection
     */
    public function removeConnection(TcpConnection $connection);

    /**
     * @param TcpConnection $connection
     *
     * @return ConnectionInterface
     */
    public function findOne(TcpConnection $connection): ConnectionInterface;

    /**
     * @return TcpConnection[]
     */
    public function findAll(): array;

    /**
     * @param TcpConnection $connection
     *
     * @return TcpConnection[]
     */
    public function findAllWithout(TcpConnection $connection): array;
}