<?php


namespace Commands;


use Slack\ChannelInterface;
use Slack\Message\Attachment;

class MillennialCapitalizationCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return ['mc', 'tc'];
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        foreach ($message as $i => $letter) {
            if ($i % 2 == 0) {
                $message[$i] = strtoupper($letter);
            }
        }
        $this->client->postMessage($this->client->getMessageBuilder()
            ->setText($message)
            ->setChannel($channel)
            ->create());
    }
}