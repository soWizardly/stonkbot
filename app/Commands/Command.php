<?php

namespace App\Commands;

use App\Communication\ConnectionManager;
use App\Communication\Message;
use Slack\ChannelInterface;

abstract class Command
{

    /**
     * Return a description of what the command does.
     * @return string
     */
    public abstract function description(): string;

    /**
     * The name of the command, or an array of aliases
     * Returning null means it has no command, and just listens to the channel.
     * @return string|array|null
     */
    public abstract function command();

    /**
     * Run the command on the specified channel.
     * @param Message $msg
     * @return mixed
     */
    public abstract function run(Message $msg): Message;
}
