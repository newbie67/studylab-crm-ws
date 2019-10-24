<?php

namespace App\Domain\Model;

use Workerman\Connection\TcpConnection;

interface FieldInterface
{
    /**
     * @param TcpConnection $connection
     *
     * @return bool
     */
    public function isLockedBy(TcpConnection $connection): bool;
}