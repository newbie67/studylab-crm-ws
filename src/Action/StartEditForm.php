<?php

namespace app\Action;

use app\Component\Model;

/**
 * Говорит всем, что текущую форму правит менеджер такой то
 *
 * @package app\Action
 */
class StartEditForm extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data)
    {
        if (empty($data->model) || empty($data->id)) {
            return ;
        }
        $model = Model::getInstance($data->model, (int)$data->id);

        $connectionStorage = $this->storage->getConnectionStorage();
        foreach ($connectionStorage->getAllWithout($this->connection->id) as $connection) {
            $connection->send(json_encode([
                'action' => 'startEdit',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'user'   => $this->prepareUserForResponse($connection),
            ]));
        }

        $lockedFields = $model->getLockedFields();
        $tmp = [];
        foreach ($lockedFields as $fieldName => $connectionId) {
            if (!isset($tmp[$connectionId])) {
                $tmp[$connectionId] = [];
            }
            $tmp[$connectionId][] = $fieldName;
        }

        foreach ($tmp as $connectionId => $fields) {
            $tcpConnection = $this->storage->getConnectionStorage()->getById($connectionId);
            $this->connection->send(json_encode([
                'action' => 'multipleFocusFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $fields,
                'user'   => $this->prepareUserForResponse($tcpConnection),
            ]));
        }
    }
}