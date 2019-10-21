<?php

namespace app\Action;

use app\Component\Model;

/**
 * Событие блокировки поля или списка полей пользователем
 *
 * @package app\Action
 */
class LockField extends AbstractAction
{
    /**
     * @inheritDoc
     * @todo отправлять нужно не всем, а только тем, кто правит форму.
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