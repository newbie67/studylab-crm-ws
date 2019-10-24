<?php

namespace App\Model;

use App\Domain\Model\FieldInterface;
use Workerman\Connection\TcpConnection;

class Field implements FieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TcpConnection
     */
    private $lockBy;

    /**
     * Field constructor.
     *
     * @param TcpConnection $connection
     * @param string        $name
     */
    public function __construct(TcpConnection $connection, string $name)
    {
        $this->lockBy = $connection;
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function isLockedBy(TcpConnection $connection): bool
    {
        return $this->lockBy->id === $connection->id;
    }
}