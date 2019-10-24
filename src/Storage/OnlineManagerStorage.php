<?php


namespace App\Storage;

use App\Domain\Storage\OnlineManagerStorageInterface;
use App\Service\ClientNotifier;
use Workerman\Connection\TcpConnection;

class OnlineManagerStorage implements OnlineManagerStorageInterface
{
    /**
     * @var array
     */
    private $managers = [];

    /**
     * @var ClientNotifier
     */
    private $clientNotifier;

    /**
     * OnlineManagerStorage constructor.
     *
     * @param ClientNotifier $clientNotifier
     */
    public function __construct(
        ClientNotifier $clientNotifier
    ) {
        $this->clientNotifier = $clientNotifier;
    }

    /**
     * @inheritDoc
     */
    public function setManagerStatus(int $managerId, TcpConnection $connection, string $status)
    {
        if (false === in_array($status, [self::STATUS_ONLINE, self::STATUS_AWAY], true)) {
            return ;
        }
        if (false === array_key_exists($managerId, $this->managers)) {
            $this->managers[$managerId] = [];
        }

        $this->managers[$managerId][$connection->id] = $status;
        // Если статус online, нотифицируем всех
        if ($status === self::STATUS_ONLINE) {
            $needNotify = true;
        } else {
            // Если у менеджера прилетел статус away, проверяем все другие статусы этого менеджера
            // Если все away - значит он действительно отошёл
            $needNotify = !in_array(self::STATUS_ONLINE, $this->managers[$managerId]);
        }
        if ($needNotify) {
            $this->clientNotifier->setManagerStatus($connection, $managerId, $status);
        }
    }

    /**
     * @inheritDoc
     */
    public function setOffline(TcpConnection $connection)
    {
        foreach ($this->managers as $managerId => $managerConnections) {
            foreach ($managerConnections as $connectionId => $status) {
                if ($connection->id === $connectionId) {
                    unset($this->managers[$managerId][$connectionId]);
                    // Если это последний коннект менеджера - уведомляем клиентов
                    if (empty($this->managers[$managerId])) {
                        $this->clientNotifier->setManagerStatus($connection, $managerId, self::STATUS_OFFLINE);
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getManagerStatus(int $managerId): string
    {
        if (empty($this->managers[$managerId])) {
            return self::STATUS_OFFLINE;
        }

        return in_array(self::STATUS_ONLINE, $this->managers[$managerId], true)
            ? self::STATUS_ONLINE
            : self::STATUS_AWAY;
    }
}