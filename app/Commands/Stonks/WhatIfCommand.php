<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use App\Facades\Container;
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
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        try {
            $msg = explode(" ", $message->getMessage());
            $stonk = strtoupper($msg[1]);
            $amount = (int)$msg[2];
            $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols={$stonk}&types=quote");
            $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($stonk, $msg, $amount, $message) {
                $resp = json_decode($response->getBody(), true);
                $message2 = [];

                foreach ($resp as $res) {
                    $change = $res["quote"]["ytdChange"] + 1;
                    $newAmount = round($amount * $change, 2);
                    $message2 = "If you invested \${$amount} Jan 1st of this year, you'd have \${$newAmount} now.";
                }

                $message->setMessage('');
                $message->setAttachments([new Attachment("What if? {$stonk}", $message2, null, $newAmount > $amount ? "#00ff00" : "#ff0000")]);
                return $message;
            });
            $promise->wait();

        } catch (\Exception $e) {
            $message->setMessage("I died: " . $e->getMessage());
            return $message;
        }
    }

    public function description(): string
    {
        return 'Would of, could of, should of.';
    }
}