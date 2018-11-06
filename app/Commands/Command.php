<?php

namespace Bot\Commands;

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
     * The name of the command
     * @return string
     */
    public abstract function command(): string;


    /**
     * Run the command on the specified channel.
     * @param string|int $channel Channel ID or Name
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public abstract function run($channel, $message);

}
