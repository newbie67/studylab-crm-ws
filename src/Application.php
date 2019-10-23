<?php

namespace App;

use App\Controller\CloseController;
use App\Controller\NewMessageController;
use Psr\Container\ContainerInterface;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Application constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerInterface::class);
    }

    /**
     * Start worker
     */
    public function run()
    {
        $port = $this->container->get('config')['port'];
        $server = new Worker('websocket://0.0.0.0:' . $port);

        $server->onWorkerStart = [$this, 'onWorkerStart'];
        $server->onWorkerStop = [$this, 'onWorkerStop'];
        $server->onWorkerReload = [$this, 'onWorkerReload'];

        $server->onConnect = [$this, 'onConnect'];
        $server->onMessage = [$this, 'onMessage'];
        $server->onClose = [$this, 'onClose'];

        Worker::runAll();
    }

    // region: Socket callbacks

    /**
     * @param TcpConnection $connection
     */
    public function onConnect(TcpConnection $connection)
    {
        $this->logger->debug('New connection # ' . $connection->id);
    }

    /**
     * @param TcpConnection $connection
     * @param string        $message
     */
    public function onMessage(TcpConnection $connection, string $message)
    {
        $this->logger->debug('New message from #' . $connection->id);
        $this->logger->debug('Message: ' . $message);

        $closeController = $this->container->get(NewMessageController::class);
        $closeController->run($connection);
    }

    /**
     * @param TcpConnection $connection
     */
    public function onClose(TcpConnection $connection)
    {
        $this->logger->debug('Connection closed #' . $connection->id);

        $closeController = $this->container->get(CloseController::class);
        $closeController->run($connection);
    }

    // endregion

    // region: Worker callbacks

    /**
     * @param Worker $worker
     */
    public function onWorkerStart(Worker $worker)
    {
        $this->logger->info('Worker started');
    }

    /**
     * @param Worker $worker
     */
    public function onWorkerStop(Worker $worker)
    {
        $this->logger->info('Worker stopped');
    }

    /**
     * @param Worker $worker
     */
    public function onWorkerReload(Worker $worker)
    {
        $this->logger->info('Worker reloaded');
    }
    // endregion
}
