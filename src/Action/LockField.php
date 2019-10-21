<?php

namespace app\Action;

use app\Component\Model;

/**
 * Говорит всем, что текущую форму правит менеджер такой то
 *
 * @package app\Action
 */
class LockField extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data)
    {
        if (empty($data->model) || empty($data->id) || empty($data->fields)) {
            return ;
        }

        $model = Model::getInstance($data->model, (int)$data->id);
        $model->lockFields($this->connection, $data->fields);

        $connectionStorage = $this->storage->getConnectionStorage();
        foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
            $connection->send(json_encode([
                'action' => 'focusFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $data->fields,
                'user'   => $this->prepareUserForResponse($connection),
            ]));
        }
    }
}