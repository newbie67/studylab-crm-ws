<?php

namespace app\Action;

use app\Domain\Storage\ConnectionStorageInterface;

/**
 * Изменение статуса менеджера.
 *
 * На одного менеджера может быть много соединений.
 * $connectionStorageInterface хранит подключения каждого окна.
 * Менеджер будет считать оффлайн, если все его соединения прервутся.
 *
 * Менеджер будет в статусе "away", если все его соединения будут в статусе "away".
 *
 *
 * @package app\Action
 */
class ChangeManagerStatus extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data)
    {
        $connectionStorage = $this->storage->getConnectionStorage();
        $managerStorage = $this->storage->getManagerStorage();
        // Если пользователь не является менеджером, отключаем его
        if (false === array_key_exists($this->request->userId(), $this->managers)) {
            return ;
        }
        if (in_array(
            $data->status,
            [ConnectionStorageInterface::STATUS_ONLINE, ConnectionStorageInterface::STATUS_AWAY]
        )) {
            // Меняем статус соединения
            $connectionStorage->setConnectionStatus($this->connection, $data->status);

            // Пользователь реально отошёл, только если все его соединения стали away
            $statusAway = true;
            $allConnections = $managerStorage->getConnectionsByManagerId($this->request->userId());

            foreach ($allConnections as $connection) {
                if ($connectionStorage->getStatus($connection) !== ConnectionStorageInterface::STATUS_AWAY) {
                    $statusAway = false;
                }
            }

            // Если реальный статус менеджера изменился, сообщаем всем остальным соединениям
            $status = $statusAway ? ConnectionStorageInterface::STATUS_AWAY : ConnectionStorageInterface::STATUS_ONLINE;
            if ($status !== $managerStorage->getStatus($this->request->userId())) {
                $managerInfo = $this->managers[$this->request->userId()];
                $managerInfo['status'] = $status;

                // Сообщаем всем коннекшенам статус пользователя
                // Todo Проверить как принимает значения фронт, может быть нужно посылать ВСЕМ, ВКЛЮЧАЯ себя
                foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
                    $connection->send(json_encode([
                        'action'   => 'changeManagersStatuses',
                        'statuses' => [
                            $this->request->userId() => $managerInfo,
                        ]
                    ]));
                }
            }
        }
    }
}
