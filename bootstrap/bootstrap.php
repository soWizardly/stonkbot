<?php
// Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment files.
$env = Dotenv\Dotenv::create(__DIR__ . '/..');
$env->load();

// Create the service container.
$container = new \Pimple\Container();
\App\Facades\Container::setContainer($container);

// Load configuration files.
$container['config'] = function () {
    return new \App\Configuration\ConfigurationManager(__DIR__ . '/../config');
};

// Register providers.
foreach ($container['config']['app']['providers'] as $provider) {
    $container->register(new $provider);
}

// Connect to the server.
$container[\App\Communication\ConnectionManager::class]->connect();

// Load the commands.
$commands = array();
foreach ($container['config']['commands'] as $command) {
    $commands[] = new $command($container[\Slack\RealTimeClient::class]);
}
