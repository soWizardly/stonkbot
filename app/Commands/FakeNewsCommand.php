<?php


namespace Bot\Commands;


use Bot\BagOfDooDoo;
use GuzzleHttp\Client;
use Slack\RealTimeClient;

class FakeNewsCommand extends Command
{

    /**
     * The name of the command
     * @return string
     */
    public function command(): string
    {
        return 'fakenews';
    }

    /**
     * Run the command
     * @param $channel
     * @param $message
     * @return mixed
     */
    public function run($channel, $message)
    {
        $httpClient = BagOfDooDoo::make(Client::class);
        $config = BagOfDooDoo::make('config');
        $client = BagOfDooDoo::make(RealTimeClient::class);

        $request = new \GuzzleHttp\Psr7\Request('GET', "https://newsapi.org/v2/top-headlines?country=us&apiKey={$config["news_api"]}");
        $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $channel) {

            $res = json_decode($response->getBody(), true);
            $article = $res["articles"][array_rand($res["articles"])];
            $dt = "Donald Trump was quoted as saying he 'loved it'.";
            $description = !empty($article["description"]) ? rtrim($article["description"], ".") . " and {$dt}" : $dt;
            $message = $client->getMessageBuilder()->addAttachment(new Attachment(
                rtrim($article["title"], ".") . " in Donald Trump's bed.",
                $description . " " . $article["url"]
            ))->setText('')->setChannel($channel)->create();

            $client->postMessage($message);
        });
        $promise->wait();
    }
}