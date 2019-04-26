<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true,

        //FRONTEND URL
        'url' => 'http://localhost:8080',

        //Mail smtp
        'smtp' => [
          'debug' => 0,
          'host' => 'host39.checkdomain.de',
          'auth' => 'true',
          'username' => 'noreply@groe.me',
          'password' => 'Ht_0hU9pl',
          'secure' => 'tls',
          'port' => 587,
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
