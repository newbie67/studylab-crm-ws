<?php

namespace app2\Action;

use app2\Component\Model;

/**
 * Событие разблокировки поля
 *
 * @package app\Action
 */
class UnLockField extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data = null)
    {
        if (empty($data->model) || empty($data->id) || empty($data->fields)) {
            return ;
        }

        $model = Model::getInstance($data->model, (int)$data->id);
        $model->unlockFields((array) $data->fields);

        $connectionStorage = $this->storage->getConnectionStorage();
        foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
            $connection->send(json_encode([
                'action' => 'blurFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $data->fields,
                'user'   => $this->prepareUserForResponse($this->connection),
            ]));
        }
    }
}