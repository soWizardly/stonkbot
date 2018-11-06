<?php
require 'bootstrap.php';

use Bot\BagOfDooDoo;
use Bot\Commands\Command;

$commandClasses = include 'commands.php';
$commands = array();
foreach ($commandClasses as $command) {
    $commands[] = new $command(BagOfDooDoo::make(\Slack\RealTimeClient::class));
}


$client->on('message', function ($data) use ($client, $httpClient, $config, $commands) {
    $client->getChannelGroupOrDMByID($data['channel'])->then(function ($channel) use ($client, $data, $httpClient, &$last, $config, $commands) {
        $action = explode(" ", strtolower($data["text"])) ?? null;
        $text = strtolower($data["text"]);

        // just hard code it for now w/e
        $token = '.';
        foreach ($commands as $command) {
            /* @var Command $command */
            if (is_array($command->command())) {
                foreach ($command->command() as $alias) {
                    if ($action[0] == $token . $alias) {
                        $command->run($channel, $action);
                    }
                }
            } else if ($action[0] == $token . $command->command()) {
                $command->run($channel, $action);
            }
        }


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


    });
});

$loop->run();
