<?php


namespace Commands;


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
     * @param $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        $message = $this->client->getMessageBuilder()
            ->setText("It's .stonk")
            ->setChannel($channel)
            ->create();
        $this->client->postMessage($message);
    }

    public function description(): string
    {
        return 'Get it right, kiddo.';
    }
}