<?php

require 'bootstrap/bootstrap.php';

$commands = array();
foreach ($container['config']['commands'] as $command) {
    $commands[] = new $command($container[\Slack\RealTimeClient::class]);
}

$client = $container[\Slack\RealTimeClient::class];
$client->on('message', function ($data) use ($container, $commands,$client) {
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
        $text = strtolower($data["text"]);

        // just hard code it for now w/e
        $token = '.';
        foreach ($commands as $command) {
            if (($token . 'help') == $action[0]) {
                $aliases = is_array($command->command()) ? implode(', ', $command->command()) : $command->command();
                $maybe = get_class($command) . ': "' . $command->description() . '"' . ' [' . $aliases . ']';
                $no = $client->getMessageBuilder()
                    ->setText($maybe)
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($no);
                continue;
            }
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


        if (rand(0, 4) == 0) {
            if (strpos($text, ':hypers:') !== false) {
                $message = $client->getMessageBuilder()
                    ->setText(":hypers:")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);
            }

            if (strpos($text, 'too careful') !== false) {
                $message = $client->getMessageBuilder()
                    ->setText("You can never be too careful when it comes to aborto-tron.")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);
            }

            if (strpos($text, 'thanks') !== false) {
                $message = $client->getMessageBuilder()
                    ->setText("You're welcome, bitch.")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);
            }
        }


    });
});


$container[\React\EventLoop\LoopInterface::class]->run();
