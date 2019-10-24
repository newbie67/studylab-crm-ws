<?php

use App\Domain\Service\CrmInterface;
use App\Domain\Storage\ConnectionStorageInterface;
use App\Domain\Storage\OnlineManagerStorageInterface;
use App\Domain\Storage\UserRelConnectionStorageInterface;
use App\Service\Crm;
use App\Storage\ConnectionStorage;
use App\Storage\OnlineManagerStorage;
use App\Storage\UserRelConnectionStorage;
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
    OnlineManagerStorageInterface::class => autowire(OnlineManagerStorage::class),
    ConnectionStorageInterface::class => autowire(ConnectionStorage::class),
    UserRelConnectionStorageInterface::class => autowire(UserRelConnectionStorage::class),
];