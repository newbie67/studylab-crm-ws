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
     * @return ModelInterface[]
     */
    public function getEditedModels(): array;
}