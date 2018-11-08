<?php

namespace Commands;

use GuzzleHttp\Client;
use Slack\ChannelInterface;

class FUDCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'btc';
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        $client = new Client();
        $response = $client->get('https://api.coinmarketcap.com/v2/ticker/');
        $json = json_decode((string)$response->getBody(), true)['data'];
        // hard coded because yolo swaggins
        $coins = ['BTC', 'ETH', 'LTC', 'LINK', 'XLM',];
        $line = '';
        foreach ($json as $item) {
            if (in_array($item['symbol'], $coins)) {
                $line .= $item['symbol'] . ': ' . $item['quotes']['USD']['price'] . ' (' . $item['quotes']['USD']['percent_change_24h'] . '%) | ';
            }
        }

        $message = $this->client->getMessageBuilder()
            ->setText($line)
            ->setChannel($channel)
            ->create();
        $this->client->postMessage($message);
    }
}