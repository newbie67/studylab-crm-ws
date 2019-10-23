<?php

namespace app2\Action;

use app2\Component\Model;

/**
 * Говорит всем, что текущую форму правит менеджер такой то
 *
 * @package app\Action
 */
class StartEditForm extends AbstractAction
{
    /**
     * @inheritDoc
     * @todo отправлять только тем, кто правит форму
     */
    public function run($data = null)
    {
        $connectionStorage = $this->storage->getConnectionStorage();

        if (empty($data->model) || empty($data->id)) {
            return ;
        }
        $model = Model::getInstance($data->model, (int)$data->id);
        $connectionStorage->setEditedForms($this->connection, $model);

        $lockedFields = $model->getLockedFields();
        $tmp = [];
        foreach ($lockedFields as $fieldName => $connectionId) {
            if (!isset($tmp[$connectionId])) {
                $tmp[$connectionId] = [];
            }
            $tmp[$connectionId][] = $fieldName;
        }


        foreach ($tmp as $connectionId => $fields) {
            $tcpConnection = $connectionStorage->getById($connectionId);
            $this->connection->send(json_encode([
                'action' => 'multipleFocusFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $fields,
                'user'   => $this->prepareUserForResponse($tcpConnection),
            ]));
        }

        foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
            $connection->send(json_encode([
                'action' => 'startEdit',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'user'   => $this->prepareUserForResponse($this->connection),
            ]));
        }
    }
}