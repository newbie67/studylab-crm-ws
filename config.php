<?php

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;

return [
    'port' => 25025,
    'backendAddr' => 'http://localhost:8080/',
    'logger' => new Logger('PhpWebSocketApplication', [
        // In File
        // new Monolog\Handler\StreamHandler(__DIR__ . '/logs.log', Logger::DEBUG),

        // STDOUT
        new ErrorLogHandler(),
    ]),
];