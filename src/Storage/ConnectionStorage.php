<?php

namespace App\Storage;

use App\Domain\Model\ModelInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use Workerman\Connection\TcpConnection;

class ConnectionStorage implements ConnectionStorageInterface
{
    /**
     * @var TcpConnection[]
     */
    private $connections = [];

    /**
     * @var ModelInterface[][]
     */
    private $models = [];

    /**
     * @inheritDoc
     */
    public function addConnection(TcpConnection $connection)
    {
        if (false === array_key_exists($connection->id, $this->connections)) {
            $this->connections[$connection->id] = $connection;
        }
    }

    /**
     * @inheritDoc
     */
    public function addEditedModel(TcpConnection $connection, ModelInterface $model)
    {
        if (false === array_key_exists($connection->id, $this->models))  {
            $this->models[$connection->id] = [];
        }

        $key = $model->getId() . '|' . $model->getName();
        $this->models[$connection->id][$key] = $model;
    }

    /**
     * @inheritDoc
     */
    public function getEditedModels(TcpConnection $connection): array 
    {
        if (array_key_exists($connection->id, $this->models))  {
            return $this->models[$connection->id];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(TcpConnection $connection)
    {
        unset($this->connections[$connection->id]);
    }

    /**
     * @inheritDoc
     */
    public function findAllWithout(TcpConnection $connection): array
    {
        $tmp = $this->connections;
        unset($tmp[$connection->id]);
        return $tmp;
    }
}
