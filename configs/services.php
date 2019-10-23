<?php

use App\Domain\Service\CrmInterface;
use App\Service\Crm;
use Psr\Log\LoggerInterface;
use function DI\autowire;

$config = require_once __DIR__ . '/config.php';

return [
    'config' => function () use ($config) {
        return $config;
    },
    LoggerInterface::class => function() use ($config) {
        return $config['logger'];
    },
    CrmInterface::class => autowire(Crm::class)
        ->constructorParameter('connectionString', $config['backendAddr'])
    ,
];