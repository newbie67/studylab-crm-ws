<?php

namespace App\Service;

use app\Domain\Model\FieldInterface;
use app\Domain\Model\ModelInterface;
use App\Domain\Service\CrmInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use App\Domain\Storage\OnlineManagerStorageInterface;
use Workerman\Connection\TcpConnection;

class ClientNotifier
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
     * @param TcpConnection $connection
     * @param int           $managerId
     * @param string        $status
     */
    public function setManagerStatus(TcpConnection $connection, int $managerId, string $status)
    {
        foreach ($this->connectionStorage->findAllWithout($connection) as $otherConnection) {
            $otherConnection->getTcpConnection()->send(json_encode([
                'action' => 'changeManagersStatuses',
                'statuses' => [
                    (string)$managerId => array_merge(
                        ['status' => $status],
                        $this->prepareUserForResponse($managerId)
                    ),
                ],
            ]));
        }
    }

    /**
     * @param TcpConnection $connection
     * @param array         $managersInfo
     */
    public function sendManagersStatuses(TcpConnection $connection, array $managersInfo)
    {
        $connection->send(json_encode([
            'action'   => 'changeManagersStatuses',
            'statuses' => $managersInfo,
        ]));
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    private function prepareUserForResponse(int $userId): array
    {
        return array_key_exists($userId, $this->crm->getAllUsers()) ? $this->crm->getAllUsers()[$userId] : [];
    }

//    /**
//     * @param TcpConnection  $whoLock
//     * @param ModelInterface $model
//     *
//     * @param array          $fields
//     */
//    public function lockFields(TcpConnection $whoLock, ModelInterface $model, array $fields)
//    {
//    }
//
////    /**
//     * @param TcpConnection    $whoLock
//     * @param ModelInterface   $model
//     * @param FieldInterface[] $fields
//     */
//    public function unlockFields(TcpConnection $whoLock, ModelInterface $model, array $fields)
//    {
//        $fieldsArray = [];
//        foreach ($fields as $field) {
//            $fieldsArray[$field->getName()] = $field->getValue();
//        }
//        foreach ($this->connectionStorage->findAllWithout($whoLock) as $connection) {
//            $connection->send(json_encode([
//                'action' => 'blurFields',
//                'model'  => $model->getModelName(),
//                'id'     => $model->getId(),
//                'fields' => $fieldsArray,
//                'user'   => $this->prepareUserForResponse($this->connection),
//            ]));
//        }
//    }
//
//    protected function prepareUserForResponse()
//    {
//        $users = $this->crm->getAllUsers();
//    }
}