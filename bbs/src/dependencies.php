<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['pdo'] = function ($c) {
    $cfg = $c->get('settings')['db'];
    return new \PDO($cfg['dsn'], $cfg['user'], $cfg['password']);
};

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        $c['renderer']->render($response, 'error.phtml');
        return $response->withStatus(403);
    };
};
