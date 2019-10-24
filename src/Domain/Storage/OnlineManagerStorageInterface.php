<?php

namespace App\Domain\Storage;

use Workerman\Connection\TcpConnection;

/**
 * Сторадж OnlineManagerStorageInterface
 * хранит в себе список онлайн-менеджеров и идентификаторы их соединения
 *
 * @package App\Domain\Storage
 */
interface OnlineManagerStorageInterface
{
    const STATUS_ONLINE = 'online';
    const STATUS_AWAY = 'away';
    const STATUS_OFFLINE = 'offline';

    /**
     * @param int           $managerId
     * @param TcpConnection $connection
     * @param string        $status
     */
    public function setManagerStatus(int $managerId, TcpConnection $connection, string $status);

    /**
     * @param TcpConnection $connection
     */
    public function setOffline(TcpConnection $connection);

    /**
     * @param int $managerId
     *
     * @return string
     */
    public function getManagerStatus(int $managerId): string;
}