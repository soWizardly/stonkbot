<?php
require_once __DIR__ . '/vendor/autoload.php';

use Slack\Message\{Attachment, AttachmentBuilder, AttachmentField};

$config = include __DIR__ . '/config/config.php';
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

BagOfDooDoo::register('config', $config);
BagOfDooDoo::register(\GuzzleHttp\Client::class, $httpClient);
BagOfDooDoo::register(\Slack\RealTimeClient::class, $client);

// Creates an empty SQLite file, if it doesn't exist..
$sqlite = new SQLite3(__DIR__ . '/storage/db.sqlite');
$ormConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/src'), true);
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/storage/db.sqlite'
);

$entityManager = \Doctrine\ORM\EntityManager::create($conn, $ormConfig);

