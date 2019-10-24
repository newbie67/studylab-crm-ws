<?php

namespace App\Domain\Storage;

use Workerman\Connection\TcpConnection;

interface ConnectionStorageInterface
{
    /**
     * @param TcpConnection $connection
     */
    public function addConnection(TcpConnection $connection);

    /**
     * @param TcpConnection $connection
     */
    public function removeConnection(TcpConnection $connection);

    /**
     * @param TcpConnection $connection
     *
     * @return TcpConnection[]
     */
    public function findAllWithout(TcpConnection $connection): array;
}