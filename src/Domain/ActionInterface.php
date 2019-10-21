<?php

namespace app\Domain;

use app\Component\ParsedRequest;
use app\Domain\Storage\StorageInterface;
use Workerman\Connection\TcpConnection;

interface ActionInterface
{
    /**
     * ActionInterface constructor.
     *
     * @param TcpConnection    $connection
     * @param StorageInterface $storage
     * @param ParsedRequest    $request
     * @param array            $allUsers
     */
    public function __construct(
        TcpConnection $connection,
        StorageInterface $storage,
        ParsedRequest $request,
        array $allUsers
    );

    /**
     * Execute current action
     *
     * @param \stdClass|null $data
     */
    public function run($data);
}