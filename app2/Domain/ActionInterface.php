<?php

namespace app2\Domain;

use app2\Component\ParsedRequest;
use app2\Domain\Storage\StorageInterface;
use Workerman\Connection\TcpConnection;

interface ActionInterface
{
    /**
     * ActionInterface constructor.
     *
     * @param TcpConnection      $connection
     * @param StorageInterface   $storage
     * @param ParsedRequest|null $request
     * @param array              $allUsers
     */
    public function __construct(
        TcpConnection $connection,
        StorageInterface $storage,
        ParsedRequest $request = null,
        array $allUsers = []
    );

    /**
     * Execute current action
     *
     * @param \stdClass $data
     */
    public function run($data = null);
}