<?php

namespace App\Domain\Storage;

use App\Domain\Model\ConnectionInterface;
use Workerman\Connection\TcpConnection;

interface ConnectionStorageInterface
{
    /**
     * @param TcpConnection $connection
     *
     * @return ConnectionInterface
     */
    public function addConnection(TcpConnection $connection) : ConnectionInterface;

    /**
     * @param TcpConnection $connection
     */
    public function removeConnection(TcpConnection $connection);
//
//    /**
//     * @param TcpConnection $connection
//     *
//     * @return ConnectionInterface
//     */
//    public function findOne(TcpConnection $connection): ConnectionInterface;

    /**
     * @return ConnectionInterface[]
     */
    public function findAll(): array;

    /**
     * @param TcpConnection $connection
     *
     * @return ConnectionInterface[]
     */
    public function findAllWithout(TcpConnection $connection): array;
}