<?php

namespace App\Service;

use App\Domain\Model\ModelInterface;
use App\Domain\Service\CrmInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use App\Domain\Storage\UserRelConnectionStorageInterface;
use Psr\Log\LoggerInterface;
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

    /**
     * @var UserRelConnectionStorageInterface
     */
    private $userRelConnectionStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ClientNotifier constructor.
     *
     * @param ConnectionStorageInterface        $connectionStorage
     * @param CrmInterface                      $crm
     * @param UserRelConnectionStorageInterface $userRelConnectionStorage
     * @param LoggerInterface                   $logger
     */
    public function __construct(
        ConnectionStorageInterface $connectionStorage,
        CrmInterface $crm,
        UserRelConnectionStorageInterface $userRelConnectionStorage,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->connectionStorage = $connectionStorage;
        $this->crm = $crm;
        $this->userRelConnectionStorage = $userRelConnectionStorage;
    }

    /**
     * @param TcpConnection $connection
     * @param int           $managerId
     * @param string        $status
     */
    public function setManagerStatus(TcpConnection $connection, int $managerId, string $status)
    {
        foreach ($this->connectionStorage->findAllWithout($connection) as $otherConnection) {
            $otherConnection->send(json_encode([
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
     * Сообщает всем клиента, что поля заблокированы
     * @param TcpConnection  $connection
     * @param ModelInterface $model
     *
     * @param array          $fields
     */
    public function lockFields(TcpConnection $connection, ModelInterface $model, array $fields)
    {
        $user = $this->userRelConnectionStorage->findUserByConnectionId($connection->id);
        if (empty($user)) {
            $this->logger->critical('Undefined user for connection #'.$connection->id);
            return ;
        }
        foreach ($this->connectionStorage->findAllWithout($connection) as $tcpConnection) {
            if ($tcpConnection->id !== $connection->id) {
                $tcpConnection->send(json_encode([
                    'action' => 'focusFields',
                    'model' => $model->getName(),
                    'id' => $model->getId(),
                    'fields' => $fields,
                    'user' => $user,
                ]));
            }
        }
    }
    
    /**
     * Сообщает всем клиентам, что поля разблокированы, а так же из значение если оно есть
     * @param TcpConnection  $connection
     * @param ModelInterface $model
     *
     * @param array          $fields
     */
    public function unlockFields(TcpConnection $connection, ModelInterface $model, array $fields)
    {
        $this->logger->debug('Значение полей для разблокировки: ' . json_encode($fields));

        $user = $this->userRelConnectionStorage->findUserByConnectionId($connection->id);
        if (empty($user)) {
            $this->logger->critical('Undefined user for connection #'.$connection->id);
            return ;
        }
        foreach ($this->connectionStorage->findAllWithout($connection) as $tcpConnection) {
            if ($tcpConnection->id !== $connection->id) {
                $tcpConnection->send(json_encode([
                    'action' => 'blurFields',
                    'model' => $model->getName(),
                    'id' => $model->getId(),
                    'fields' => $fields,
                    'user' => $user,
                ]));
            }
        }
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    private function prepareUserForResponse(int $userId): array
    {
        return array_key_exists($userId, $this->crm->getAllUsers())
            ? $this->crm->getAllUsers()[$userId]
            : [];
    }
}