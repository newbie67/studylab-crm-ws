<?php

namespace app\Action;

class ChangeManagerStatus extends AbstractAction
{
    /**
     * Возможные статусы менеджеров
     */
    const MANAGER_STATUS_ONLINE = 'online';
    const MANAGER_STATUS_AWAY = 'away';
    const MANAGER_STATUS_OFFLINE = 'offline';

    /**
     * @inheritDoc
     */
    public function run($data)
    {
        // доделываю
        if ($data->status === self::MANAGER_STATUS_ONLINE) {
            $this->storage->getManagerStorage()->setManagerStatus(
                $this->request->getManagerId(),
                self::MANAGER_STATUS_ONLINE
            );
        }
        var_dump($data);
        //
        // TODO: Implement run() method.
    }
}