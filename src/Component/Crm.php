<?php

namespace app\Component;

use app\Domain\Component\CrmInterface;
use GuzzleHttp\Client;

class Crm implements CrmInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Crm constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function isValidToken(int $id, string $token)
    {
        // TODO: Implement isValidToken() method.
        return true;
    }

    /**
     * @inheritDoc
     *
     * @todo Добавить кеширование активных менеджеров, минут на 10
     */
    public function getManagers()
    {
        $response = $this->client->get('api/WebSocketCallback_getManagers');
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['data'];
    }
}