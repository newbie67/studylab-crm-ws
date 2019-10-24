<?php

namespace App\Model;

use App\Domain\Model\ConnectionInterface;
use Workerman\Connection\TcpConnection;

class Connection implements ConnectionInterface
{
    /**
     * @var TcpConnection
     */
    private $tcpConnection;

    /**
     * @var ConnectionInterface[]
     */
    private static $instances = [];

    /**
     * @inheritDoc
     */
    public static function getInstance(TcpConnection $connection): ConnectionInterface
    {
        if (false === array_key_exists($connection->id, self::$instances)) {
            self::$instances[$connection->id] = new self($connection);
        }

        return self::$instances[$connection->id];
    }

    /**
     * @inheritDoc
     */
    public function getTcpConnection(): TcpConnection
    {
        return $this->tcpConnection;
    }





    public function getEditedModels(): array
    {
        // TODO: Implement getEditedModels() method.
    }

    /**
     * Connection constructor.
     *
     * @param TcpConnection $connection
     */
    private function __construct(TcpConnection $connection)
    {
        $this->tcpConnection = $connection;
    }
}