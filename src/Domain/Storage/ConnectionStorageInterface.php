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
    const STATUS_ONLINE = 'online';
    const STATUS_AWAY = 'away';
    const STATUS_OFFLINE = 'offline';

    /**
     * Добавляет текущий коннек
     *
     * @param TcpConnection $connection
     * @param int           $userId
     */
    public function addConnection(TcpConnection $connection, int $userId);

    /**
     * Удаляет текущий коннек
     *
     * @param TcpConnection $connection
     */
    public function removeConnection(TcpConnection $connection);

    /**
     * @return TcpConnection[]
     */
    public function getAll(): array;

    /**
     * @param int $id
     *
     * @return TcpConnection[]
     */
    public function getAllWithout(int $id): array;

    /**
     * Возвращает коннект по его ID
     *
     * @param int $id
     *
     * @return TcpConnection|null
     */
    public function getById(int $id);

    /**
     * @param TcpConnection $connection
     *
     * @return int
     */
    public function getTimeStart(TcpConnection $connection): int;

    /**
     * @param TcpConnection $connection
     *
     * @return string
     */
    public function getStatus(TcpConnection $connection): string;

    /**
     * @param TcpConnection $connection
     * @param string        $status
     */
    public function setConnectionStatus(TcpConnection $connection, string $status);

    /**
     * @param TcpConnection $connection
     *
     * @return int|null
     */
    public function getUserId(TcpConnection $connection);
}