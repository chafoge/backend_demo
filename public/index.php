<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
error_reporting(-1);
ini_set('display_errors', 'On');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

date_default_timezone_set("Europe/Berlin");

require __DIR__ . '/../vendor/autoload.php';

session_start();

//Controller Namespace
$controller_namespace = '\src\Requests\\';

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/lists.php';
require __DIR__ . '/../src/directorys.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();

//php -S localhost:8080 -t public /index.php
