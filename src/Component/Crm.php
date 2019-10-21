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
        $response = $this->client->post('api/WebSocketCallback_checkToken', [
            'form_params' => [
                'id' => $id,
                'token' => $token,
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);

        return (bool) @$data['data']['success'];
    }

    /**
     * @inheritDoc
     *
     * @todo Можно добавить кеширование активных менеджеров, минут на 10
     *       Чтобы не дёргать CRM при каждом запросе
     */
    public function getManagers()
    {
        $response = $this->client->get('api/WebSocketCallback_getManagers');
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['data'];
    }
}