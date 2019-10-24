<?php

namespace App\Domain\Storage;

use App\Domain\Model\ModelInterface;
use Workerman\Connection\TcpConnection;

interface ConnectionStorageInterface
{
    /**
     * @param TcpConnection $connection
     */
    public function addConnection(TcpConnection $connection);

    /**
     * @param TcpConnection  $connection
     * @param ModelInterface $model
     */
    public function addEditedModel(TcpConnection $connection, ModelInterface $model);

    /**
     * @param TcpConnection $connection
     *
     * @return ModelInterface[]
     */
    public function getEditedModels(TcpConnection $connection): array;

    /**
     * @param TcpConnection $connection
     */
    public function removeConnection(TcpConnection $connection);

    /**
     * @param TcpConnection $connection
     *
     * @return TcpConnection[]
     */
    public function findAllWithout(TcpConnection $connection): array;
}