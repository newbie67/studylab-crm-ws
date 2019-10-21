<?php

namespace app;

use app\Action\ChangeManagerStatus;
use app\Component\Crm;
use app\Component\RequestParser;
use app\Domain\ActionInterface;
use app\Domain\Component\CrmInterface;
use app\Domain\Storage\StorageInterface;
use app\Storage\Storage;
use GuzzleHttp\Client;
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
     * Роутинг
     */
    const ACTIONS_MAP = [
        'changeManagerStatus' => ChangeManagerStatus::class,
    ];

    /**
     * @var Worker
     */
    private $server;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CrmInterface
     */
    private $crm;

    /**
     * @var RequestParser
     */
    private $requestParser;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->server = new Worker('websocket://0.0.0.0:' . $config['port']);

        $this->logger = $config['logger'];
        $guzzle = new Client(['base_uri' => $config['backendAddr']]);
        $this->crm = new Crm($guzzle);
        $this->requestParser = new RequestParser($this->logger);

        $this->storage = new Storage();
    }

    /**
     * Запускает WS-сервер и регистрирует коллбеки
     */
    public function run()
    {
        $logger = $this->logger;
        $crm = $this->crm;
        $requestParser = $this->requestParser;
        $storage = $this->storage;

        $this->setWorkerCallbacks();
        $this->server->count = -1; // Unlimited connection count

        /**
         * Коллбек на установленное соединение
         *
         * @param TcpConnection $connection
         */
        $this->server->onConnect = function (TcpConnection $connection) use ($logger) {
            $this->logger->info('New connection. Connection->id: ' . $connection->id);
        };

        /**
         * Коллбек на входящее сообщение
         *
         * @param TcpConnection $connection
         * @param               $data
         *
         * @return void|bool
         */
        $this->server->onMessage = function (TcpConnection $connection, $data) use (
            $logger,
            $crm,
            $requestParser,
            $storage
        ) {
            $logger->info('Got new message:' . (string)$data);

            // Проверка валидности запроса
            if (false === $requestParser->isValidRequest($data)) {
                return false;
            }

            $parsedRequest = $requestParser->getParsedRequest($data);
            // Если токен передан верно
            if (false === $crm->isValidToken($parsedRequest->getManagerId(), $parsedRequest->getToken())) {
                return false;
            }

            // Отлавливаем текущий роут и запускаем метод
            if (array_key_exists($parsedRequest->getAction(), self::ACTIONS_MAP)) {
                // добавляем коннекшн в сторадж, если он ещё не добавлен и завязываем на менеджера
                $storage->getConnectionStorage()->addConnection($connection, $parsedRequest->getManagerId());
                $storage->getManagerStorage()->addManagerConnection($parsedRequest->getManagerId(), $connection);

                $actionClassName = self::ACTIONS_MAP[$parsedRequest->getAction()];
                /** @var ActionInterface $action */
                $action = new $actionClassName($connection, $storage, $parsedRequest, $crm->getManagers());
                $action->run($parsedRequest->getData());
            } else {
                $this->logger->error('Undefined action ' . $parsedRequest->getAction());
            }
        };

        /**
         * Коллбек на закрытие соединения
         *
         * @param TcpConnection $connection
         */
        $this->server->onClose = function (TcpConnection $connection) use ($logger) {
            // Прекращаем правки текущего соединения в полях и сообщаем клиентам
            // Если это последний коннект менеджера - говорим всем, что он оффлайн
            $logger->info('Connection closed. Connection->id: ' . $connection->id);
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
            $logger->info('Worker started');
        };
        $this->server->onWorkerStop = function (Worker $worker) use ($logger) {
            $logger->info('Worker stopped');
        };
        $this->server->onWorkerReload = function (Worker $worker) use ($logger) {
            $logger->info('Worker reloaded');
        };
    }
}
