<?php

namespace App\Commands;


use App\Communication\Message;
use Slack\ChannelInterface;

class TipsHatCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'ladies';
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $message->setMessage("( ͡° ͜ʖ ͡° )\n( ͡⊙ ͜ʖ ͡⊙ )\n( ͡◉ ͜ʖ ͡◉ )\n");
        return $message;
    }


    public function description(): string
    {
        return 'm\'ladies, *tips fedora*';
    }
}