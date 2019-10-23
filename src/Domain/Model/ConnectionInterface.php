<?php

namespace App\Domain\Model;

use Workerman\Connection\TcpConnection;

interface ConnectionInterface
{
    /**
     * @param TcpConnection $tcpConnection
     * @param int           $userId
     */
    public function __construct(TcpConnection $tcpConnection, int $userId);
}