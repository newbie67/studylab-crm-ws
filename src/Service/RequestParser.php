<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use stdClass;

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
     * @param string $message
     *
     * @return stdClass|bool False в случае, если запрос невалидный JSON
     */
    public function parse(string $message)
    {
        $parsedData = json_decode($message);

        if (
            false === is_object($parsedData)
            || json_last_error() !== JSON_ERROR_NONE
        ) {
            $this->logger->error('Frontend sent bad message: ', ['body' => $message]);
            return false;
        }

        return $parsedData;
    }
}