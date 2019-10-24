<?php

namespace App\Controller;

use Workerman\Connection\TcpConnection;
use stdClass;

abstract class AbstractController
{
    /**
     * @param TcpConnection $currentConnection
     * @param stdClass|null   $data
     */
    abstract public function run(TcpConnection $currentConnection, stdClass $data = null);
}