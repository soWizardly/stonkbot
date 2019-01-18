<?php


namespace App\Commands\APIs;


use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;

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
     * @param Message $message
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $httpClient = resolve(Client::class);
        $config = resolve('config');

        $request = new \GuzzleHttp\Psr7\Request('GET',
            "https://newsapi.org/v2/top-headlines?country=us&apiKey={$config["news_api"]}");
        $promise = $httpClient->sendAsync($request)->then(function ($response) use ($message) {
            $res = json_decode($response->getBody(), true);
            $article = $res["articles"][array_rand($res["articles"])];
            $dt = "Donald Trump was quoted as saying he 'loved it'.";
            $description = !empty($article["description"]) ? rtrim($article["description"], ".") . " and {$dt}" : $dt;

            $attachment = new \App\Communication\Attachment(rtrim($article["title"], ".") . " in Donald Trump's bed.", $description . " " . $article["url"]);
            return (new Message($message->getChannel(), "", [$attachment]));
        });
        return $promise->wait();
    }

    public function description(): string
    {
        return 'Not you, no, not you. Quiet please. No, sir, quite. You are fake news.';
    }
}