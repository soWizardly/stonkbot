<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
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
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        $config = resolve('config');
        try {
            $stonk = $message[1];
            if (empty($stonk)) {
                throw new \Exception("you need to enter a stonk");
            }

            $from = date("Y-m-d", strtotime("yesterday"));
            /**
             * Required attribution to newsapi.org
             */
            $request = new \GuzzleHttp\Psr7\Request('GET', "https://newsapi.org/v2/everything?q={$stonk}&from={$from}&apiKey={$config["news_api"]}");
            $promise = Container::make(Client::class)->sendAsync($request)->then(function ($response) use ($channel) {
                $res = json_decode($response->getBody(), true);
                $message = $this->client->getMessageBuilder();
                $sources = [];
                foreach ($res["articles"] as $article) {
                    if (in_array($article["source"]["id"], $sources) || count($sources) > 3) {
                        continue;
                    }
                    $sources[] = $article["source"]["id"];
                    $message = $message->addAttachment(new Attachment(
                        $article["title"],
                        $article["description"] . " " . $article["url"]
                    ));
                }
                $this->client->postMessage($message->setText('')->setChannel($channel)->create());
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
        return 'Calculated, thoroughly thought out fake news.';
    }
}