<?php
require 'bootstrap/bootstrap.php';


$client = $container[\Slack\RealTimeClient::class];

$client->on('message', function ($data) use ($container, $commands, $client) {
    $httpClient = $container[\GuzzleHttp\Client::class];

    $client->getChannelGroupOrDMByID($data['channel'])->then(function ($channel) use (
        $client,
        $data,
        $httpClient,
        &
        $last,
        $commands
    ) {
        $action = explode(" ", strtolower($data["text"])) ?? null;
        // Commands
        // just hard code it for now w/e
        $token = config('app')['token'];
        foreach ($commands as $command) {
            /* @var Command $command */
            if (is_array($command->command())) {
                foreach ($command->command() as $alias) {
                    if ($action[0] == $token . $alias) {
                        $command->run($channel, $action);
                    }
                }
            } else {
                if ($action[0] == $token . $command->command()) {
                    $command->run($channel, $action);
                }
            }
        }
    });
});


$container[\App\Communication\ConnectionManager::class]->run();
