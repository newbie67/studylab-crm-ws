<?php

use App\Application;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/vendor/autoload.php';

/** @var ContainerInterface $container */
$container = require_once __DIR__ . '/configs/container.php';

$application = new Application($container);
$application->run();
