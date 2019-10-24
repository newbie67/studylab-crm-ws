<?php

namespace App\Service;

use app\Domain\Model\FieldInterface;
use app\Domain\Model\ModelInterface;
use App\Domain\Service\CrmInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use Workerman\Connection\TcpConnection;

class ClientNotifierHelper
{
    /**
     * @var ConnectionStorageInterface
     */
    private $connectionStorage;

    /**
     * @var CrmInterface
     */
    private $crm;

    public function __construct(
        ConnectionStorageInterface $connectionStorage,
        CrmInterface $crm
    ) {
        $this->connectionStorage = $connectionStorage;
        $this->crm = $crm;
    }

    /**
     * @param TcpConnection  $whoLock
     * @param ModelInterface $model
     *
     * @param array          $fields
     */
    public function lockFields(TcpConnection $whoLock, ModelInterface $model, array $fields)
    {
    }

    /**
     * @param TcpConnection    $whoLock
     * @param ModelInterface   $model
     * @param FieldInterface[] $fields
     */
    public function unlockFields(TcpConnection $whoLock, ModelInterface $model, array $fields)
    {
        $fieldsArray = [];
        foreach ($fields as $field) {
            $fieldsArray[$field->getName()] = $field->getValue();
        }
        foreach ($this->connectionStorage->findAllWithout($whoLock) as $connection) {
            $connection->send(json_encode([
                'action' => 'blurFields',
                'model'  => $model->getModelName(),
                'id'     => $model->getId(),
                'fields' => $tmp,
                'user'   => $this->prepareUserForResponse($this->connection),
            ]));
        }


    }
    public function prepareUserForResponse()
    {
        $users = $this->crm->getAllUsers();
        
    }
}