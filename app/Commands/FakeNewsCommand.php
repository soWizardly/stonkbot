<?php


namespace App\Commands;


use Container;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;
use Slack\RealTimeClient;

class FakeNewsCommand extends Command
{

    /**
     * The name of the command
     * @return string
     */
    public function command()
    {
        return 'fakenews';
    }

    /**
     * Run the command
     * @param $channel
     * @param $message
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        $httpClient = Container::make(Client::class);
        $config = Container::make('config');

        $request = new \GuzzleHttp\Psr7\Request('GET',
            "https://newsapi.org/v2/top-headlines?country=us&apiKey={$config["news_api"]}");
        $promise = $httpClient->sendAsync($request)->then(function ($response) use ($channel) {

            $res = json_decode($response->getBody(), true);
            $article = $res["articles"][array_rand($res["articles"])];
            $dt = "Donald Trump was quoted as saying he 'loved it'.";
            $description = !empty($article["description"]) ? rtrim($article["description"], ".") . " and {$dt}" : $dt;
            $message = $this->client->getMessageBuilder()->addAttachment(new Attachment(
                rtrim($article["title"], ".") . " in Donald Trump's bed.",
                $description . " " . $article["url"]
            ))->setText('')->setChannel($channel)->create();

            $this->client->postMessage($message);
        });
        $promise->wait();
    }

    public function description(): string
    {
        return 'Not you, no, not you. Quiet please. No, sir, quite. You are fake news.';
    }
}