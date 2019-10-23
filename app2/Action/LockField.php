<?php

namespace app2\Action;

use app2\Component\Model;

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
    public function run($data = null)
    {
        if (empty($data->model) || empty($data->id) || empty($data->fields)) {
            return ;
        }

        $model = Model::getInstance($data->model, (int)$data->id);
        $this->storage->getConnectionStorage()->setEditedForms($this->connection, $model);
        $model->lockFields($this->connection, $data->fields);

        $connectionStorage = $this->storage->getConnectionStorage();
        foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
            $connection->send(json_encode([
                'action' => 'focusFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $data->fields,
                'user'   => $this->prepareUserForResponse($this->connection),
            ]));
        }
    }
}