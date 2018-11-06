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
     * @param string|int $channel
     * @return mixed
     */
    public abstract function run($channel);

}
