<?php
require_once __DIR__ . '/../vendor/autoload.php';

$env = Dotenv\Dotenv::create(__DIR__ . '/..');
$env->load();

$container = new \Pimple\Container();
$container['config'] = function () {
    return new \App\Configuration\ConfigurationManager(__DIR__ . '/../config');
};
foreach ($container['config']['app']['providers'] as $provider) {
    $container->register(new $provider);
}


// TODO(vulski): Add a connection abstraction interface.
$container[\Slack\RealTimeClient::class]->connect();



