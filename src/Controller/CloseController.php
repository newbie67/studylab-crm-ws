<?php

namespace App\Controller;

use App\Domain\Storage\ConnectionStorageInterface;
use App\Service\ClientNotifierHelper;
use Psr\Log\LoggerInterface;
use Workerman\Connection\TcpConnection;

class CloseController extends AbstractController
{
    /**
     * @var ConnectionStorageInterface
     */
    private $connectionStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientNotifierHelper
     */
    private $clientNotifierHelper;

    public function __construct(
        LoggerInterface $logger,
        ConnectionStorageInterface $connectionStorage,
        ClientNotifierHelper $clientNotifierHelper
    ) {

    }

    /**
     * @inheritDoc
     *
     * @param null $data
     */
    public function run(TcpConnection $currentConnection, string $data = null)
    {
        $connection = $this->connectionStorage->findOne($currentConnection);
        $models = $connection->getEditedModels();
        foreach ($models as $model) {
            $unlockedFields = [];
            foreach ($model->getLockedFields() as $field) {
                if ($field->isLockedBy($currentConnection)) {
                    $field->unlock();
                    $unlockedFields[] = $field;
                }
            }
            $this->logger->debug(
                'Unlock fields for model ' . $model->getName() . ' id #' . $model->getId(),
                ['body' => serialize($unlockedFields)]
            );

            $this->clientNotifierHelper->unlockFields($currentConnection, $model, $unlockedFields);
        }


        /**
         *

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
        $userId = $connectionStorage->getUserId($this->connection);
        $connectionStorage->removeConnection($this->connection);

        if (null !== $userId) {
        $managerStorage->removeManagerConnection($userId, $this->connection);

        // Если это последний коннект менеджера - говорим всем, что он оффлайн
        $otherConnections = $managerStorage->getConnectionsByManagerId($userId);
        if (empty($otherConnections)) {
        foreach ($connectionStorage->getAll() as $tcpConnection) {
        $tcpConnection->send(json_encode([
        'action'   => 'changeManagersStatuses',
        'statuses' => [
        $userId => array_merge(
        $this->prepareUserForResponse($this->connection),
        ['status' => ConnectionStorageInterface::STATUS_OFFLINE]
        ),
        ]
        ]));
        }
        }
        }
         */

        $this->connectionStorage->removeConnection($currentConnection);
    }
}