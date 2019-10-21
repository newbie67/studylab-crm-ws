<?php

namespace app\Action;

use app\Domain\Storage\ConnectionStorageInterface;

/**
 * Возвращает список мееджеров и их статусы
 *
 * @package app\Action
 */
class GetManagersStatuses extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data)
    {
        $managerStorage = $this->storage->getManagerStorage();

        $result = [];
        foreach ($this->managers as $managerId => $manager) {
            $status = $managerStorage->getStatus($managerId);
            if (null === $status) {
                $status = ConnectionStorageInterface::STATUS_OFFLINE;
            }
            $result[$managerId] = array_merge($manager, ['status' => $status]);
        }
        $this->connection->send(json_encode([
            'action'   => 'changeManagersStatuses',
            'statuses' => $result,
        ]));
    }
}