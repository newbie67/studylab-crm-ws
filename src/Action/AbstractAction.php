<?php

namespace app\Action;

use app\Component\ParsedRequest;
use app\Domain\ActionInterface;
use app\Domain\Storage\StorageInterface;
use Workerman\Connection\TcpConnection;

abstract class AbstractAction implements ActionInterface
{
    /**
     * @var TcpConnection
     */
    protected $connection;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ParsedRequest
     */
    protected $request;

    /**
     * Все пользователи
     *
     * @var array
     */
    protected $users;

    /**
     * Только менеджеры
     *
     * @var array
     */
    protected $managers;

    /**
     * @inheritDoc
     */
    public function __construct(
        TcpConnection $connection,
        StorageInterface $storage,
        ParsedRequest $request,
        array $allUsers
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        $this->request = $request;

        foreach ($allUsers as $user) {
            $this->users[(int)$user['id']] = $user;
            if ($user['role'] === 'manager') {
                $this->managers[(int)$user['id']] = $user;
            }
        }
    }

    /**
     * @param TcpConnection $connection
     *
     * @todo
     * @return array
     */
    protected function prepareUserForResponse(TcpConnection $connection): array
    {
        $userId = $this->storage->getConnectionStorage()->getUserId($connection);
        return $this->users[$userId];
    }
}
