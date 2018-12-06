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
        return ['btc', 'crapto', 'crypto', 'wheremybeans', 'amirich', 'fud', 'moonedyet'];
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
        // IM A GOOD PROGRAMMER
        array_shift($message);
        if (empty($message)) {
            $coins = ['BTC', 'ETH', 'LTC', 'LINK', 'BTCSV', 'XLM',];
        } else {
            $coins = array_map(function ($row) {
                return strtoupper($row);
            }, $message);
        }
        $line = '';
        $i = 0;
        foreach ($json as $item) {
            if (in_array($item['symbol'], $coins)) {
                $line .= $item['symbol'] . ': ' . round($item['quotes']['USD']['price'],
                        3) . ' (' . $item['quotes']['USD']['percent_change_24h'] . '%)';
                $i++;
                if ($i != count($coins)) {
                    $line .= ' | ';
                }
            }
        }

        $message = $this->client->getMessageBuilder()
            ->setText($line)
            ->setChannel($channel)
            ->create();
        $this->client->postMessage($message);
    }

    public function description(): string
    {
        return ':moon: :lambo: :linkies: :bigmac:';
    }
}