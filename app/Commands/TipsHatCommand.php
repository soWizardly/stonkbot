<?php

namespace App\Commands;


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
    public function run(ChannelInterface $channel, $message)
    {
        $pm = function ($message) use ($channel) {
            $message = $this->client->getMessageBuilder()
                ->setText($message)
                ->setChannel($channel)
                ->create();
            $this->client->postMessage($message);
        };
        $pm("( ͡° ͜ʖ ͡° )");
        $pm("( ͡⊙ ͜ʖ ͡⊙ )");
        $pm("( ͡◉ ͜ʖ ͡◉ )");
    }


    public function description(): string
    {
        return 'm\'ladies, *tips fedora*';
    }
}