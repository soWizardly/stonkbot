<?php


namespace App\Commands;


use App\Container;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class WhatIfCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'whatif';
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

            $stonk = strtoupper($message[1]);
            $amount = (int)$message[2];
            $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols={$stonk}&types=quote");
            $promise = Container::make(Client::class)->sendAsync($request)->then(function ($response) use ($stonk, $channel, $amount) {

                $resp = json_decode($response->getBody(), true);
                $message2 = [];

                foreach ($resp as $res) {
                    $change = $res["quote"]["ytdChange"] + 1;
                    $newAmount = round($amount * $change, 2);
                    $message2 = "If you invested \${$amount} Jan 1st of this year, you'd have \${$newAmount} now.";
                }

                $message2 = $this->client->getMessageBuilder()
                    ->addAttachment(
                        new Attachment("What if? {$stonk}", $message2, null, $newAmount > $amount ? "#00ff00" : "#ff0000")
                    )
                    ->setText('')
                    ->setChannel($channel)
                    ->create();
                $this->client->postMessage($message2);
            });
            $promise->wait();

        } catch (\Exception $e) {

            $message = $this->client->getMessageBuilder()
                ->setText("I died: " . $e->getMessage())
                ->setChannel($channel)
                ->create();
            $this->client->postMessage($message);

        }
    }

    public function description(): string
    {
        return 'Would of, could of, should of.';
    }
}