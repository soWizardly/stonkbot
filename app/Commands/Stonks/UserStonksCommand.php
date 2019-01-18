<?php


namespace App\Commands\Stonks;


use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class UserStonksCommand extends Command
{

    private $users;

    public function __construct()
    {
        $this->users = config('user_stonks');
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
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $msg = explode(" ", $message->getMessage());
        if (in_array(str_replace('.', '', $msg[0]), array_keys($this->users))) {
            $isLong = trim($msg[1] ?? '');

            try {
                $stonks = implode(',', $this->users[str_replace('.', '', $msg[0])]);
                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols=" . $stonks . "&types=quote");
                $promise = resolve(Client::class)->sendAsync($request)->then(function ($response) use ($isLong, $message) {

                    $portfolio = json_decode($response->getBody(), true);
                    $msg = [];
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

                        $msg[] = strtoupper($item) . " ({$percentage}%)";
                    }

                    $move = round((array_sum($total["up"] ?? []) - array_sum($total["down"] ?? [])) / count($portfolio), 2);
                    $peRatio = array_filter($peRatio);
                    $finalMsg =
                        "Total Change " . $move .
                        "% \n Average P/E Ratio:  " . round(array_sum($peRatio) / count($peRatio), 2) . "x" .
                        (!empty($isLong) ? ("\n" . implode(', ', $msg)) : "");

                    $attachments = [new Attachment("Today's Moves", $message, null, $move > 0 ? "#00ff00" : "#ff0000")];
                    return new Message($message->getChannel(), $finalMsg, $attachments);
                });
                return $promise->wait();

            } catch (\Exception $e) {
                $message->setMessage("WTF BRO");
                return $message;
            }
        }
    }

    public function description(): string
    {
        return 'See the daily growth.';
    }
}