<?php

namespace App\Commands;


use Slack\ChannelInterface;

class ReplyChanceCommand extends Command
{

    /**
     * Return a description of what the command does.
     * @return string
     */
    public function description(): string
    {
        return "maybe maybe not";
    }

    /**
     * The name of the command, or an array of aliases
     * Returning null means it has no command, and just listens to the channel.
     * @return string|array|null
     */
    public function command()
    {
        return null;
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        // TODO: Implement run() method.
    }
}