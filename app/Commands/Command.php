<?php

namespace App\Commands;

use Slack\ChannelInterface;

abstract class Command
{

    /**
     * Slack Client
     * @var \Slack\RealTimeClient
     */
    public $client;


    /**
     * Return a description of what the command does.
     * @return string
     */
    public abstract function description(): string;

    public function __construct(\Slack\RealTimeClient $client)
    {
        $this->client = $client;
    }

    /**
     * The name of the command, or an array of aliases
     * Returning null means it has no command, and just listens to the channel.
     * @return string|array|null
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
