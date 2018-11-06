<?php

namespace Commands;

use Slack\ChannelInterface;

abstract class Command
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
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public abstract function command();


    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public abstract function run(ChannelInterface $channel, $message);

}
