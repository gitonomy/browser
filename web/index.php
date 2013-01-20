<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

$repositories = require __DIR__.'/../config.php';
$app = new Gitonomy\Browser\Application($repositories);
$app['debug'] = true;
$app->run();
