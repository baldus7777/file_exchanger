<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = require 'settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require 'dependencies.php';
// Register middleware
require 'middleware.php';
// Register routes
require 'routes.php';

$app->add($container->csrf);
