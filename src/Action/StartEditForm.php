<?php

namespace app\Action;

use app\Component\Model;
use Workerman\Connection\TcpConnection;

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
        
        // отмечаем, что пользователь правит
       /**
        // Говорим текущему юзеру, какие поля кто правит
        if (!empty($this->_currentModel)) {
            $lockedFields = $this->_currentModel->getLockedFields();
        }

        if (!empty($lockedFields)) {
            $reverseLockedFields = [];
            foreach ($lockedFields as $fieldName => $userId) {
                if (!isset($reverseLockedFields[$userId])) {
                    $lockedFields[$userId] = [];
                }
                $reverseLockedFields[$userId][] = $fieldName;
            }
            $responseLockedFields = [
                'action'      => 'multipleFocusFields',
                'model'       => $this->_currentModel->getModelName(),
                'id'          => $this->_currentModel->getId(),
                'focusFields' => [],
            ];
            // "@todo: Говорим текущему юзеру, кто ещё правит поля
            foreach ($reverseLockedFields as $clientId => $fields) {
                $responseLockedFields['focusFields'][] = [
                    'fields' => $fields,
                    'user'   => $this->_getUserDataForResponse($clientId),
                ];
            }
            $this->_send($connectionId, $responseLockedFields);
        }

        */
    }
}