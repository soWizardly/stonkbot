<?php

use GuzzleHttp\Client;

require 'bootstrap.php';

$commandClasses = include 'config/commands.php';
$commands = array();
foreach ($commandClasses as $command) {
    $commands[] = new $command(BagOfDooDoo::make(\Slack\RealTimeClient::class));
}


$client->on('message', function ($data) use ($client, $httpClient, $config, $commands) {
    $client->getChannelGroupOrDMByID($data['channel'])->then(function ($channel) use (
        $client,
        $data,
        $httpClient,
        &
        $last,
        $config,
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

        if (rand(0, 199) == 0) {
            $xd = "What the fuck did you just fucking say about me, you little bitch? I'll have you know I graduated top of my class in the Navy Seals, and I've been involved in numerous secret raids on Al-Quaeda, and I have over 300 confirmed kills. I am trained in gorilla warfare and I'm the top sniper in the entire US armed forces. You are nothing to me but just another target. I will wipe you the fuck out with precision the likes of which has never been seen before on this Earth, mark my fucking words. You think you can get away with saying that shit to me over the Internet? Think again, fucker. As we speak I am contacting my secret network of spies across the USA and your IP is being traced right now so you better prepare for the storm, maggot. The storm that wipes out the pathetic little thing you call your life. You're fucking dead, kid. I can be anywhere, anytime, and I can kill you in over seven hundred ways, and that's just with my bare hands. Not only am I extensively trained in unarmed combat, but I have access to the entire arsenal of the United States Marine Corps and I will use it to its full extent to wipe your miserable ass off the face of the continent, you little shit. If only you could have known what unholy retribution your little "clever" comment was about to bring down upon you, maybe you would have held your fucking tongue. But you couldn't, you didn't, and now you're paying the price, you goddamn idiot. I will shit fury all over you and you will drown in it. You're fucking dead, kiddo.";
            $message = $client->getMessageBuilder()
                ->setText($xd)
                ->setChannel($channel)
                ->create();
                $client->postMessage($message);
        }
        if (rand(0, 150) == 0) {
            $xd = "Don't👏 pretend👏 to 👏be 👏entitled👏 to👏 financial👏 compensation👏 if 👏you 👏or👏 a👏 loved 👏one 👏hasn't👏 even 👏been 👏diagnosed👏 with 👏mesothelioma";
            $message = $client->getMessageBuilder()
                ->setText($xd)
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
        }


        if (rand(0, 150) == 0) {
            $xd = "Don't👏 pretend👏 to 👏be 👏entitled👏 to👏 financial👏 compensation👏 if 👏you 👏or👏 a👏 loved 👏one 👏hasn't👏 even 👏been 👏diagnosed👏 with 👏mesothelioma";
            $message = $client->getMessageBuilder()
                ->setText($xd)
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
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

$loop->run();
