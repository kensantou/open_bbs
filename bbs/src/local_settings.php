<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'image_path' => [
            'tmp_image' => __DIR__ . '/../public/uploads/tmp/',
            'public_image' => __DIR__ . '/../public/uploads/',
        ],

        // DB settings
        'db' => [
           'dsn' => 'mysql:dbname=bbssys;host=localhost;charset=utf8',
           'user' => 'bbssys',
           'password' => 'bbssys',
        ],
    ],
];
