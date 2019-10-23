<?php

namespace app2\Action;

use app2\Component\Model;

/**
 * Говорит всем, что текущую форму перестал править менеджер
 *
 * @package app\Action
 */
class EndEditForm extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($data = null)
    {
        if (empty($data->model) || empty($data->id)) {
            return ;
        }
        $model = Model::getInstance($data->model, (int)$data->id);

        $lockedFields = $model->getLockedFields();
        $tmp = [];
        $recipientConnectionIds = [];
        foreach ($lockedFields as $fieldName => $connectionId) {
            if ($connectionId = $this->connection->id) {
                $tmp[] = $fieldName;
            } else {
                $recipientConnectionIds[] = $connectionId;
            }
        }

        foreach ($recipientConnectionIds as $id) {
            $tcpConnection = $this->storage->getConnectionStorage()->getById($id);
            $tcpConnection->send(json_encode([
                'action' => 'endEdit',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $tmp,
                'user'   => $this->prepareUserForResponse($this->connection),
            ]));
        }
    }
}