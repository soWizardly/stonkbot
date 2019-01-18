<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

// Register commands.
$commands = array();
foreach ($container['config']['commands'] as $command) {
    $commands[] = new $command();
}
$container['commands'] = $commands;
