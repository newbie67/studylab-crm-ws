<?php

namespace App\Controller;

use App\Domain\Storage\ConnectionStorageInterface;
use App\Domain\Storage\OnlineManagerStorageInterface;
use App\Service\ClientNotifier;
use Psr\Log\LoggerInterface;
use Workerman\Connection\TcpConnection;
use stdClass;

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
     * @var ClientNotifier
     */
    private $clientNotifier;

    /**
     * @var OnlineManagerStorageInterface
     */
    private $onlineManagerStorage;

    public function __construct(
        LoggerInterface $logger,
        ConnectionStorageInterface $connectionStorage,
        ClientNotifier $clientNotifier,
        OnlineManagerStorageInterface $onlineManagerStorage
    ) {
        $this->logger = $logger;
        $this->connectionStorage = $connectionStorage;
        $this->clientNotifier = $clientNotifier;
        $this->onlineManagerStorage = $onlineManagerStorage;
    }

    /**
     * @inheritDoc
     *
     * @param null $data
     */
    public function run(TcpConnection $currentConnection, stdClass $data = null)
    {
        foreach ($this->connectionStorage->getEditedModels($currentConnection) as $model) {
            $tmp = [];
            $fields = $model->getFields();
            foreach ($fields as $fieldName => $field) {
                if ($field->isLockedBy($currentConnection)) {
                    $tmp[$fieldName] = null;
                }
            }
            if (!empty($tmp)) {
                $this->clientNotifier->unlockFields($currentConnection, $model, $tmp);
            }
        }

        $this->connectionStorage->removeConnection($currentConnection);
        $this->onlineManagerStorage->setOffline($currentConnection);
    }
}
