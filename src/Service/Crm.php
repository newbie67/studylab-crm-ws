<?php

namespace App\Service;

use App\Domain\Service\CrmInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class Crm implements CrmInterface
{
    // Сколько кешировать список всех менеджеров
    const CACHE_TIME = 60 * 10;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $connectionString;

    /**
     * @var array
     */
    private $allUser;

    /**
     * @var array
     */
    private $managers;

    /**
     * @var int|null
     */
    private $lastUpdatedTime;

    /**
     * Crm constructor.
     * @param string          $connectionString
     * @param LoggerInterface $logger
     * @param Client          $client
     */
    public function __construct(
        string $connectionString,
        LoggerInterface $logger,
        Client $client
    ) {
        $this->client = $client;
        $this->connectionString = $connectionString;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function isValidToken(int $managerId, string $token): bool
    {
        $response = $this->client->post($this->connectionString . 'api/WebSocketCallback_checkToken', [
            'form_params' => [
                'id' => $managerId,
                'token' => $token,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents());

        return (bool) @$data->data->success;
    }

    /**
     * @inheritDoc
     */
    public function getManagers(): array
    {
        if (
            null === $this->managers
            || null === $this->lastUpdatedTime
            || ($this->lastUpdatedTime + self::CACHE_TIME) < time()
        ) {
            $this->updateUsers();
        }

        return $this->managers;
    }

    /**
     * @inheritDoc
     */
    public function getAllUsers(): array
    {
        if (
            null === $this->allUser
            || null === $this->lastUpdatedTime
            || ($this->lastUpdatedTime + self::CACHE_TIME) < time()
        ) {
            $this->updateUsers();
        }

        return $this->allUser;
    }

    /**
     * Обновляет список пользователей
     */
    private function updateUsers()
    {
        $data = $this->getResponse('get', 'api/WebSocketCallback_getActiveUsers');

        if (!isset($data['data'])) {
            $this->logger->error('CRM returned bad data: ', ['body' => json_encode($data)]);
        }

        $this->managers = [];
        $this->allUser = [];

        foreach ($data['data'] as $user) {
            $this->allUser[$user['id']] = $user;
            if ($user['role'] === 'manager') {
                $this->managers[$user['id']] = $user;
            }
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return array
     */
    private function getResponse(string $method, string $uri, array $options = []) : array
    {
        try {
            $response = $this->client->request($method, $this->connectionString . $uri, $options);
            $responseString = $response->getBody()->getContents();
            $data = json_decode($responseString, true);

            if (
                false === is_array($data)
                || json_last_error() !== JSON_ERROR_NONE
            ) {
                $this->logger->critical('CRM returned bad response: ', ['body' => $responseString]);
                return [];
            }

            return $data;
        } catch (GuzzleException $e) {
            $this->logger->critical($e->getMessage(), ['body' => $e->getTraceAsString()]);
        }

        return [];
    }
}