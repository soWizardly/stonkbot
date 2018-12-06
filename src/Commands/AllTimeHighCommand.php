<?php


namespace Commands;


use BagOfDooDoo;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
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
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
            try {

                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/QQQ/batch?types=quote");
                $promise = BagOfDooDoo::make(Client::class)->sendAsync($request)->then(function ($response) use ($channel) {

                    $res = json_decode($response->getBody(), true);
                    $ath = 186.74;
                    $isAth = \round($res["quote"]["latestPrice"], 2) >= $ath;

                    $message = $this->client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment(
                                "Are we at an all time high?",
                                $isAth ? ":hypers: YES THE FUCK WE ARE!!!!! :hypers:" : "Nah son, QQQ was at {$ath} the other day", null, $isAth ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $this->client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $this->client->getMessageBuilder()
                    ->setText("WTF")
                    ->setChannel($channel)
                    ->create();
                $this->client->postMessage($message);

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