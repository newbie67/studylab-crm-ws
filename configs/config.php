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
    'port' => 36326,
    'backendAddr' => 'http://crm.studylab.ru/',
    'logger' => new Logger('PhpWebSocketApplication', [
        $stdoutLoggerHandler,
        $fileHandler,
    ]),
];