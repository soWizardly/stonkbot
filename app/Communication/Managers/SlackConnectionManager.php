<?php

namespace App\Communication\Managers;


use App\Communication\ConnectionManager;
use App\Communication\Message;
use GuzzleHttp\Client;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Slack\RealTimeClient;

class SlackConnectionManager implements ConnectionManager
{


    /**
     * @var RealTimeClient
     */
    private $slackClient;

    protected $loop;

    /**
     * SlackConnectionManager constructor.
     * @param RealTimeClient $client
     * @param LoopInterface $loop
     */
    public function __construct(RealTimeClient $client, LoopInterface $loop)
    {
        $this->slackClient = $client;
        $this->loop = $loop;
    }

    /**
     * Connect to a server.
     * @return Promise
     */
    public function connect(): PromiseInterface
    {
        return $this->slackClient->connect();
    }

    /**
     * Disconnect from a server.
     */
    public function disconnect()
    {
        $this->loop->stop();
        $this->slackClient->disconnect();
    }

    /**
     * Send a message to a server and channel.
     * @param Message $msg
     * @return bool
     */
    public function sendMessage(Message $msg): bool
    {
        $this->slackClient->getMessageBuilder()
            ->setText($msg->getMessage())
            ->setChannel($msg->getChannel());
    }

    /**
     * Start the loop.
     */
    public function run()
    {
        $this->loop->run();
    }
}