<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require_once __DIR__ . '/config.php';

(new \app\Application($config))->run();
