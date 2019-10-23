<?php

namespace app2\Component;

use Psr\Log\LoggerInterface;

class RequestParser
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RequestParser constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $data
     * @return bool
     */
    public function isValidRequest(string $data) : bool
    {
        $parsedBody = json_decode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Ошибка при парсинге json запроса: ' . $data);
            return false;
        }
        if (
            empty($parsedBody->action)
            || empty($parsedBody->token)
            || empty($parsedBody->id)
        ) {
            $this->logger->error('В запросе не хватает данных: ' . $data);
            return false;
        }

        return true;
    }

    /**
     * @param string $data
     *
     * @return ParsedRequest
     */
    public function getParsedRequest(string $data) : ParsedRequest
    {
        $parsedBody = json_decode($data);

        return new ParsedRequest(
            $parsedBody->action,
            $parsedBody->token,
            (int)$parsedBody->id,
            isset($parsedBody->data) ? $parsedBody->data : null
        );
    }
}