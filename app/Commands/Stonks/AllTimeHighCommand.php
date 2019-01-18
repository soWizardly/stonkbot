<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use React\Promise\PromiseInterface;
use Slack\Message\Attachment;

class AllTimeHighCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'ath';
    }

    /**
     * Run the command on the specified channel.
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        try {
            $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/QQQ/batch?types=quote");
            /** @var Promise $promise */
            $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($message) {
                $res = json_decode($response->getBody(), true);
                $ath = 186.74;
                $isAth = \round($res["quote"]["latestPrice"], 2) >= $ath;
                $attachment = new Attachment(
                    "Are we at an all time high?",
                    $isAth ? ":hypers: YES THE FUCK WE ARE!!!!! :hypers:" : "Nah son, QQQ was at {$ath} the other day", null, $isAth ? "#00ff00" : "#ff0000");
                    return new Message($message->getChannel(), "", [$attachment]);
            });
            return $promise->wait();
        } catch (\Exception $e) {
            return new Message($message->getChannel(), "WTF: " . $e->getMessage());
        }
    }

    /**
     * Return a description of what the command does.
     * @return string
     */
    public function description(): string
    {
        return 'ARE WE THERE YET';
    }
}