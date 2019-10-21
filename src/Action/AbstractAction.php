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
        array $managers
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        $this->request = $request;

        foreach ($managers as $manager) {
            $this->managers[(int)$manager['id']] = $manager;
        }
    }
}
