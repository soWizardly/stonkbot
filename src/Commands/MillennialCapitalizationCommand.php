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
        array_shift($message);
        $message = str_split(implode(' ', $message));

        for ($i = 0; $i < count($message); $i++) {
            if ($i % 2 >= 1) {
                $message[$i] = strtoupper($message[$i]);
            }
        }
        $this->client->postMessage($this->client->getMessageBuilder()
            ->setText(implode($message))
            ->setChannel($channel)
            ->create());
    }

    public function description(): string
    {
        return 'Proper capitalization of the current generation.';
    }
}