<?php

namespace App\Domain\Model;

use Workerman\Connection\TcpConnection;

/**
 * Хранит всю нужную информацию об одном соединении
 *
 * @package App\Domain\Model
 */
interface ConnectionInterface
{
    /**
     * @param TcpConnection $connection
     *
     * @return ConnectionInterface
     */
    public static function getInstance(TcpConnection $connection): ConnectionInterface;

    /**
     * @return TcpConnection
     */
    public function getTcpConnection(): TcpConnection;





    /**
     * @return ModelInterface[]
     */
    public function getEditedModels(): array;
}