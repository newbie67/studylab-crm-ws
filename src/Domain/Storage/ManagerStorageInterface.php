<?php

namespace app\Domain\Storage;

use Workerman\Connection\TcpConnection;

/**
 * Interface ManagerStorageInterface
 *
 * @package app\Domain\Storage
 */
interface ManagerStorageInterface
{
    /**
     * Добавляет текущий коннек
     *
     * @param int           $managerId
     * @param TcpConnection $connection
     */
    public function addManagerConnection(int $managerId, TcpConnection $connection);

    /**
     * Возвращает список коннекшненов менеджера
     * @param int $managerId
     *
     * @return TcpConnection[]
     */
    public function getConnectionsByManagerId(int $managerId): array;

    /**
     * @param TcpConnection $connection
     *
     * @return int
     */
    public function getManagerIdByConnection(TcpConnection $connection): int;

    /**
     * @param int $managerId
     *
     * @return string
     */
    public function getStatus(int $managerId);

    /**
     * @param int    $managerId
     * @param string $status
     */
    public function setStatus(int $managerId, string $status);
}