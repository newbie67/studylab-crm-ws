<?php

namespace app2\Action;

use app2\Component\ParsedRequest;
use app2\Domain\ActionInterface;
use app2\Domain\Storage\StorageInterface;
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
     * При закрытии соединения экшн может отсутствовать
     * @var ParsedRequest|null
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
        ParsedRequest $request = null,
        array $allUsers = []
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        if (null !== $request) {
            $this->request = $request;
        }
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
     * @return array
     */
    protected function prepareUserForResponse(TcpConnection $connection): array
    {
        $userId = $this->storage->getConnectionStorage()->getUserId($connection);
        return $this->users[$userId];
    }
}
