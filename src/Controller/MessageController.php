<?php

namespace App\Controller;

use App\Domain\Service\CrmInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use App\Domain\Storage\OnlineManagerStorageInterface;
use App\Domain\Storage\UserRelConnectionStorageInterface;
use App\Model\Model;
use App\Service\ClientNotifier;
use Psr\Log\LoggerInterface;
use Workerman\Connection\TcpConnection;
use stdClass;

class MessageController extends AbstractController
{
    /**
     * Ключом является название вызываемого фронтендом экшена
     * @var string[]
     */
    private $actionMap = [
        'changeManagerStatus' => 'changeCurrentStatusAction',
        'getManagersStatuses' => 'getManagerStatusesAction',
        'startEdit' => 'startEditFormAction',
        'endEdit' => 'endEditFormAction',
        'focusFields' => 'lockFieldsAction',
        'blurFields' => 'unlockFieldsAction',
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CrmInterface
     */
    private $crm;

    /**
     * @var OnlineManagerStorageInterface
     */
    private $onlineManagerStorage;

    /**
     * @var ClientNotifier
     */
    private $clientNotifier;

    /**
     * @var ConnectionStorageInterface
     */
    private $connectionStorage;

    /**
     * @var UserRelConnectionStorageInterface
     */
    private $userRelConnectionStorage;

    /**
     * MessageController constructor.
     *
     * @param LoggerInterface                   $logger
     * @param CrmInterface                      $crm
     * @param OnlineManagerStorageInterface     $onlineManagerStorage
     * @param ClientNotifier                    $clientNotifier
     * @param ConnectionStorageInterface        $connectionStorage
     * @param UserRelConnectionStorageInterface $userRelConnectionStorage
     */
    public function __construct(
        LoggerInterface $logger,
        CrmInterface $crm,
        OnlineManagerStorageInterface $onlineManagerStorage,
        ClientNotifier $clientNotifier,
        ConnectionStorageInterface $connectionStorage,
        UserRelConnectionStorageInterface $userRelConnectionStorage
    ) {
        $this->crm = $crm;
        $this->logger = $logger;
        $this->onlineManagerStorage = $onlineManagerStorage;
        $this->clientNotifier = $clientNotifier;
        $this->connectionStorage = $connectionStorage;
        $this->userRelConnectionStorage = $userRelConnectionStorage;
    }

    /**
     * @param stdClass $data
     *
     * @return bool
     */
    public function isAllowed(stdClass $data): bool
    {
        if (empty($data->id) || empty($data->token) || empty($data->action)) {
            $this->logger->critical('Frontend send not enough data: ', ['body' => json_encode($data)]);
            return false;
        }
        if (false === array_key_exists($data->action, $this->actionMap)) {
            $this->logger->critical('Undefined action: ' . $data->action);
            return false;
        }
        if (false === $this->crm->isValidToken((int)$data->id, $data->token)) {
            $this->logger->info('Invalid token. UserId: #' . $data->id);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     *
     * @param string $data
     */
    public function run(TcpConnection $currentConnection, stdClass $data = null)
    {
        $this->connectionStorage->addConnection($currentConnection);
        $this->userRelConnectionStorage->addRelation($currentConnection->id, (int)$data->id);
        $action = $this->actionMap[$data->action];
        $this->{$action}($currentConnection, $data);
    }

    /**
     * @param TcpConnection $currentConnection
     * @param stdClass      $data
     */
    protected function changeCurrentStatusAction(TcpConnection $currentConnection, stdClass $data)
    {
        if (empty($data->id) || empty($data->data->status)) {
            $this->logger->warning('Incorrect incoming data ' . json_encode($data));
            return ;
        }

        $status = $data->data->status;
        $managerId = (int)$data->id;
        // Если пользователь не является менеджером, не храним его статус
        if (false === array_key_exists($managerId, $this->crm->getManagers())) {
            return ;
        }

        if ($status === OnlineManagerStorageInterface::STATUS_ONLINE) {
            $this->onlineManagerStorage->setManagerStatus($managerId, $currentConnection, $status);
        } elseif ($status === OnlineManagerStorageInterface::STATUS_AWAY) {
            $this->onlineManagerStorage->setManagerStatus($managerId, $currentConnection, $status);
        }
    }

    /**
     * @param TcpConnection $currentConnection
     * @param stdClass      $data
     */
    protected function getManagerStatusesAction(TcpConnection $currentConnection, stdClass $data)
    {
        $managers = $this->crm->getManagers();
        foreach ($managers as $managerId => $managerInfo) {
            $managers[$managerId]['status'] = $this->onlineManagerStorage->getManagerStatus($managerId);
        }
        $this->clientNotifier->sendManagersStatuses($currentConnection, $managers);
    }

    /**
     * @todo Этот экшн пока бесполезен
     */
    protected function startEditFormAction(TcpConnection $currentConnection, stdClass $data)
    {
    }

    /**
     * @todo Этот экшн пока бесполезен
     */
    protected function endEditFormAction(TcpConnection $currentConnection, stdClass $data)
    {
    }

    /**
     *  {
     *      "token":"...",
     *      "id":"5",
     *      "action":"focusFields",
     *      "data":{
     *          "model":"Contacts",
     *          "id":"36004",
     *          "fields":[
     *              "last_name",
     *              "last_name_en"
     *          ]
     *      }
     *  }
     * @param TcpConnection $currentConnection
     * @param stdClass      $data
     */
    protected function lockFieldsAction(TcpConnection $currentConnection, stdClass $data)
    {
        if (empty($data->data->model) || empty($data->data->id) || empty($data->data->fields)) {
            $this->logger->warning('Incorrect incoming data ' . json_encode($data));
            return ;
        }

        $model = Model::getInstance((int)$data->data->id, $data->data->model);
        $this->connectionStorage->addEditedModel($currentConnection, $model);
        $model->addEditBy($currentConnection);

        foreach ($data->data->fields as $field) {
            $model->lockField($currentConnection, $field);
        }
        $this->logger->debug('Lock fields: ' . json_encode((array)$data->data->fields));
        $this->clientNotifier->lockFields($currentConnection, $model, (array)$data->data->fields);
    }

    /**
     *  {
     *      "token":"...",
     *      "id":"5",
     *      "action":"blurFields",
     *      "data":{
     *          "model":"Contacts",
     *          "id":"36004",
     *          "fields":{
     *              "last_name":"12312312312 12312312 3123 1233 123 123 12",
     *              "last_name_en":"12312312312 12312312 3123 1233 123 123 12"
     *          }
     *      }
     *  }
     * @param TcpConnection $currentConnection
     * @param stdClass      $data
     */
    protected function unlockFieldsAction(TcpConnection $currentConnection, stdClass $data)
    {
        if (empty($data->data->model) || empty($data->data->id) || empty($data->data->fields)) {
            $this->logger->warning('Incorrect incoming data ' . json_encode($data));
            return ;
        }

        $model = Model::getInstance((int)$data->data->id, $data->data->model);
        $model->addEditBy($currentConnection);

        foreach ($data->data->fields as $fieldName => $fieldValue) {
            $model->unlockField($fieldName);
        }

        $this->clientNotifier->unlockFields($currentConnection, $model, (array)$data->data->fields);
    }
}
