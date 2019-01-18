<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class StonkNewsCommand extends Command
{


    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return 'stonknews';
    }

    /**
     * Run the command on the specified channel.
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $config = resolve('config');
        try {
            $stonk = $message[1];
            if (empty($stonk)) {
                return new Message($message->getChannel(), "you need to enter a stonk");
            }
            $from = date("Y-m-d", strtotime("yesterday"));
            /**
             * Required attribution to newsapi.org
             */
            $request = new \GuzzleHttp\Psr7\Request('GET', "https://newsapi.org/v2/everything?q={$stonk}&from={$from}&apiKey={$config["news_api"]}");
            $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($message) {
                $res = json_decode($response->getBody(), true);
                $attachments = [];
                $sources = [];
                foreach ($res["articles"] as $article) {
                    if (in_array($article["source"]["id"], $sources) || count($sources) > 3) {
                        continue;
                    }
                    $sources[] = $article["source"]["id"];
                    $attachments[] = new Attachment(
                        $article["title"],
                        $article["description"] . " " . $article["url"]
                    );
                }
                return new Message($message->getChannel(), "", $attachments);
            });
            return $promise->wait();
        } catch (\Exception $e) {
            return new Message($message->getChannel(), "I died: " . $e->getMessage());
        }
    }

    public function description(): string
    {
        return 'Calculated, thoroughly thought out fake news.';
    }
}