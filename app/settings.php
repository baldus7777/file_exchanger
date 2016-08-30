<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'views_path' => __DIR__ . '/../views/',
        ],
        //Database settings
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'sammdb',
            'username' => 'root',
            'password' => '1234',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],
];
