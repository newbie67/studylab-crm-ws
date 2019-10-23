<?php

namespace App\Controller;

use Workerman\Connection\TcpConnection;

abstract class AbstractController
{
    /**
     * @param TcpConnection $currentConnection
     * @param string|null   $data
     */
    abstract public function run(TcpConnection $currentConnection, string $data = null);
}