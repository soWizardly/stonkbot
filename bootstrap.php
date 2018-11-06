<?php
require __DIR__ . '/vendor/autoload.php';

use Slack\Message\{Attachment, AttachmentBuilder, AttachmentField};

$config = include __DIR__ . '/config.php';
$loop = \React\EventLoop\Factory::create();
$httpClient = new GuzzleHttp\Client([
    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
    'verify' => false
]);
$client = new \Slack\RealTimeClient($loop, new GuzzleHttp\Client([
    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
    'verify' => false
]));
$client->setToken($config["slack_token"]);
$client->connect();

\Bot\BagOfDooDoo::register('config', $config);
\Bot\BagOfDooDoo::register(\GuzzleHttp\Client::class, $httpClient);
\Bot\BagOfDooDoo::register(\Slack\RealTimeClient::class, $client);

