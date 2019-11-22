<?php

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

$stdoutLoggerHandler = new ErrorLogHandler();
$stdoutLoggerHandler->setFormatter(
    new LineFormatter(">>> %message% (%level_name% | %datetime%) %context% %extra%\n", 'Y.m.d H:i:s')
);

$fileHandler = new StreamHandler(__DIR__ . '/../../ws.error.log', Logger::ERROR);

return [
    'port' => 25025,
    'backendAddr' => 'http://localhost:8080/',
    'logger' => new Logger('PhpWebSocketApplication', [
        $stdoutLoggerHandler,
        $fileHandler,
    ]),
];