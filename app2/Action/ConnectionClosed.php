<?php

namespace app2\Action;


use app2\Domain\Storage\ConnectionStorageInterface;

/**
 * Разблокирует поля, которые правит менеджер
 *
 * @package app\Action
 */
class ConnectionClosed extends AbstractAction
{
    /**
     * @inheritDoc
     *
     * @param null $data
     */
    public function run($data = null)
    {
        $connectionStorage = $this->storage->getConnectionStorage();
        $managerStorage = $this->storage->getManagerStorage();

        // Прекращаем правки текущего соединения в полях и сообщаем клиентам
        $models = $connectionStorage->getEditedForms($this->connection);
        foreach ($models as $model) {
            $tmp = [];
            $lockedFields = $model->getLockedFields();
            foreach ($lockedFields as $field => $connectionId) {
                if ($connectionId === $this->connection->id) {
                    $tmp[] = $field;
                }
            }
            var_dump($tmp);
            $other = $connectionStorage->getAllWithout($this->connection->id);
            foreach ($other as $otherConnection) {
                $otherConnection->send(json_encode([
                    'action' => 'blurFields',
                    'model'  => $model->getModelName(),
                    'id'     => $model->getId(),
                    'fields' => $tmp,
                    'user'   => $this->prepareUserForResponse($this->connection),
                ]));
            }
        }
    }
}