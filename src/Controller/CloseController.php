<?php

namespace App\Controller;

use Workerman\Connection\TcpConnection;

class CloseController extends AbstractController
{
    public function __construct()
    {

    }

    /**
     * @inheritDoc
     *
     * @param null $data
     */
    public function run(TcpConnection $currentConnection, string $data = null)
    {

    }
}