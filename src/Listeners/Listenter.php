<?php

use Slack\ChannelInterface;

abstract class Listener
{
    /**
     * Slack Client
     * @var \Slack\RealTimeClient
     */
    public $client;


    public function __construct(\Slack\RealTimeClient $client)
    {
        $this->client = $client;
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public abstract function listen(ChannelInterface $channel, $message);
}