<?php

namespace App\Commands\APIs;

use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;

class CryptoCurrencyCommand extends Command
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
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $client = new Client();
        $response = $client->get('https://api.coinmarketcap.com/v2/ticker/');
        $json = json_decode((string)$response->getBody(), true)['data'];
        $parsed = explode(" ", $message->getMessage());
        // IM A GOOD PROGRAMMER
        array_shift($parsed);
        if (empty($parsed)) {
            $coins = ['BTC', 'ETH', 'LTC', 'LINK', 'BTCSV', 'XLM',];
        } else {
            $coins = array_map(function ($row) {
                return strtoupper($row);
            }, $parsed);
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

        return new Message($message->getChannel(), $line);
    }

    public function description(): string
    {
        return ':moon: :lambo: :linkies: :bigmac:';
    }
}