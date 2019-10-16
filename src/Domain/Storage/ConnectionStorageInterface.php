<?php

namespace app\Domain\Storage;

use Workerman\Connection\TcpConnection;

/**
 * Interface ConnectionStorageInterface
 *
 * @package app\Domain\Storage
 */
interface ConnectionStorageInterface
{
    /**
     * Добавляет текущий коннек
     *
     * @param TcpConnection $connection
     */
    public function addConnection(TcpConnection $connection);

    /**
     * @return TcpConnection[]
     */
    public function getAll();

    /**
     * @param int $id
     *
     * @return TcpConnection[]
     */
    public function getAllWithout(int $id);

    /**
     * Возвращает коннект по его ID
     *
     * @param int $id
     *
     * @return TcpConnection|null
     */
    public function getById(int $id);
}