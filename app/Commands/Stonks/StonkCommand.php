<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use Container;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class StonkCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'stonk';
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        try {
            $stonks = $message;
            unset($stonks[0]);
            $count = count($stonks);
            if ($count > 1) {
                $stonk = strtoupper(implode(',', $message));
                $request = new \GuzzleHttp\Psr7\Request('GET',
                    "https://api.iextrading.com/1.0/stock/market/batch?symbols={$stonk}&types=quote");
            } elseif ($count == 1) {
                $stonk = strtoupper($stonks[1]);
                $request = new \GuzzleHttp\Psr7\Request('GET',
                    "https://api.iextrading.com/1.0/stock/$stonk/batch?types=quote");
            } else {
                throw new \Exception("You need to enter a stock");
            }

            $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($message) {

                $resp = json_decode($response->getBody(), true);
                $message = [];

                foreach ($resp as $res) {

                    $res = $res["quote"] ?? $res;

                    $now = \round($res["latestPrice"], 2);
                    $percentage = $res["changePercent"] * 100;
                    $symbol = $res["symbol"];
                    $message[] = "{$symbol} \${$now} ({$percentage}%)";
                }

                $attachment = new Attachment("Hot Stonk Action", implode(', ', $message), null, $percentage > 0 ? "#00ff00" : "#ff0000");
                return new Message($message->getChannel(), '', [$attachment]);
            });
            return $promise->wait();

        } catch (\Exception $e) {
            return new Message($message->getChannel(), "yOu kIlLeD mE AUGHHGHG OOHHH AHHHH OOF :hypers:: " . $e->getMessage());
        }

    }

    public function description(): string
    {
        return 'Panic selling at it\;s greatest';
    }
}