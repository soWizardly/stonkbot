<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class UserStonksCommand extends Command
{

    private $users;

    public function __construct(\Slack\RealTimeClient $client)
    {
        $this->users = config('user_stonks');
        parent::__construct($client);
    }

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return array_keys($this->users);
    }

    /**
     * Run the command on the specified channel.
     * @param ChannelInterface $channel
     * @param array $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(ChannelInterface $channel, $message)
    {
        if (in_array(str_replace('.', '', $message[0]), array_keys($this->users))) {
            $isLong = trim($message[1] ?? '');

            try {
                $stonks = implode(',', $this->users[str_replace('.', '', $message[0])]);
                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols=" . $stonks . "&types=quote");
                $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($isLong, $channel) {

                    $portfolio = json_decode($response->getBody(), true);
                    $message = [];
                    $peRatio = [];

                    foreach ($portfolio as $item => $data) {

                        $peRatio[] = $data["quote"]["peRatio"];
                        $open = \round($data["quote"]["previousClose"], 2);
                        $now = \round($data["quote"]["latestPrice"], 2);
                        if ($now > $open) {
                            $percentage = \round((($now / $open) - 1) * 100, 2);
                            $total["up"][] = $percentage;
                        } elseif ($now < $open) {
                            $percentage = \round((($open / $now) - 1) * 100, 2);
                            $total["down"][] = $percentage;
                            $percentage = "-" . $percentage;
                        } elseif ($open == $now) {
                            $percentage = 0;
                        }

                        $message[] = strtoupper($item) . " ({$percentage}%)";
                    }

                    $move = round((array_sum($total["up"] ?? []) - array_sum($total["down"] ?? [])) / count($portfolio), 2);
                    $peRatio = array_filter($peRatio);
                    $message =
                        "Total Change " . $move .
                        "% \n Average P/E Ratio:  " . round(array_sum($peRatio) / count($peRatio), 2) . "x" .
                        (!empty($isLong) ? ("\n" . implode(', ', $message)) : "");


                    $message = $this->client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("Today's Moves", $message, null, $move > 0 ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $this->client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {
                var_dump($e);
                $message = $this->client->getMessageBuilder()
                    ->setText("WTF bro!")
                    ->setChannel($channel)
                    ->create();
                $this->client->postMessage($message);

            }
        }
    }

    public function description(): string
    {
        return 'See the daily growth.';
    }
}