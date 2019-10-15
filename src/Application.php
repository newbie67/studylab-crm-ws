<?php

namespace app;

use app\Domain\AuthenticatorInterface;
use Psr\Log\LoggerInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

/**
 * Class Application
 *
 * @package app
 */
class Application
{
    /**
     * @var Worker
     */
    private $server;

    /**
     * @var string
     */
    private $backendAddr;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->server = new Worker('websocket://0.0.0.0:' . $config['port']);
        $this->backendAddr = $config['backendAddr'];

        $this->logger = $config['logger'];
    }

    /**
     * Запускает WS-сервер и регистрирует коллбеки
     */
    public function run()
    {
        $this->setWorkerCallbacks();
        $this->server->count = -1; // Unlimited connection count

        $logger = $this->logger;

        $authenticator = $this->authenticator;

        $this->server->onConnect = function (TcpConnection $connection) use ($logger) {
            $this->logger->debug('New connection. Connection->id: ' . $connection->id);
        };

        $this->server->onMessage = function (TcpConnection $connection, $data) use ($logger) {
            $logger->debug('Got new message:' . (string)$data);
        };

        $this->server->onClose = function (TcpConnection $connection) use ($logger) {
            $logger->debug('Connection closed. Connection->id: ' . $connection->id);
        };

        Worker::runAll();
    }

    /**
     * Устанавливает коллбеки самого ws-сервера
     */
    private function setWorkerCallbacks()
    {
        $logger = $this->logger;

        $this->server->onWorkerStart = function (Worker $worker) use ($logger) {
            $logger->debug('Worker started');
        };
        $this->server->onWorkerStop = function (Worker $worker) use ($logger) {
            $logger->debug('Worker stopped');
        };
        $this->server->onWorkerReload = function (Worker $worker) use ($logger) {
            $logger->debug('Worker reloaded');
        };
    }
}
