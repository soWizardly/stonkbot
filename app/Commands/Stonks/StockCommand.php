<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use Slack\ChannelInterface;

class StockCommand extends Command
{

    /**
     * The name of the command, and/or its aliases
     * @return string|array
     */
    public function command()
    {
        return ['stock', 'stonks'];
    }

    /**
     * Run the command on the specified channel.
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        return new Message($message->getChannel(), "It's stonk.");
    }

    public function description(): string
    {
        return 'Get it right, kiddo.';
    }
}