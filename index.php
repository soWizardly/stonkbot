<?php

require __DIR__ . '/vendor/autoload.php';

use Slack\Message\{Attachment, AttachmentBuilder, AttachmentField};

$httpClient = new GuzzleHttp\Client([
    'curl' => [ CURLOPT_SSL_VERIFYPEER => false ],
    'verify' => false
]);

$loop   = \React\EventLoop\Factory::create();
$client = new \Slack\RealTimeClient($loop, new GuzzleHttp\Client([
    'curl' => [ CURLOPT_SSL_VERIFYPEER => false ],
    'verify' => false
]));
$client->setToken('xoxb-312718511745-419791035296-CPQzFAHQUJjkidwPAaCfVODT');
$client->connect();

$client->on('message', function ($data) use ($client, $httpClient) {
    $client->getChannelGroupOrDMByID($data['channel'])->then(function ($channel) use ($client, $data, $httpClient,&$last) {
    
        $action = explode(" ", strtolower($data["text"])) ?? null;
        $text = strtolower($data["text"]);

        if ($action[0] == ".stock" || $action[0] == ".stonks") {
            $message = $client->getMessageBuilder()
                ->setText("It's .stonk")
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
        }

        if ($action[0] == ".ath") {

            try {

                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/QQQ/batch?types=quote");
                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $channel) {

                    $res = json_decode($response->getBody(), true);
                    $ath = 186.74;
                    $isAth  = \round($res["quote"]["latestPrice"],2) >= $ath;

                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment(
                                "Are we at an all time high?",
                                $isAth ? ":hypers: YES THE FUCK WE ARE!!!!! :hypers:" : "Nah son, QQQ was at {$ath} the other day", null, $isAth ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("WTF")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }
        }

        if (strpos($text, ':hypers:') !== false) {
            $message = $client->getMessageBuilder()
                ->setText(":hypers:")
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
        }

        if (strpos($text, 'too careful') !== false) {
            $message = $client->getMessageBuilder()
                ->setText("You can never be too careful when it comes to aborto-tron.")
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
        }

        if (strpos($text, 'thanks') !== false) {
            $message = $client->getMessageBuilder()
                ->setText("You're welcome, bitch.")
                ->setChannel($channel)
                ->create();
            $client->postMessage($message);
        }

        if ($action[0] == ".stonk") {

            try {

                $stonks = $action;
                unset($stonks[0]);

                $count = count($stonks);

                if ($count > 1) {
                    $stonk  = strtoupper(implode(',',$action));
                    $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols={$stonk}&types=quote");
                } elseif ($count == 1) {
                    $stonk = strtoupper($stonks[1]);
                    $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/$stonk/batch?types=quote");
                } else {
                    throw new \Exception("You need to enter a stock");
                }

                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $stonk, $channel) {

                    $resp = json_decode($response->getBody(), true);
                    $message = [];

                    foreach ($resp as $res) {

                        $res = $res["quote"] ?? $res;

                        $now        = \round($res["latestPrice"],2);
                        $percentage = $res["changePercent"] * 100;
                        $symbol     = $res["symbol"];
                        $message[]  = "{$symbol} \${$now} ({$percentage}%)";
                    }

                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("Hot Stonk Action", implode(', ',$message), null, $percentage > 0 ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);
                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("I died: ".$e->getMessage())
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }

        }

        if ($action[0] == ".whatif") {

            try {

                $stonk   = strtoupper($action[1]);
                $amount  = (int)$action[2];
                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols={$stonk}&types=quote");
                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $stonk, $channel, $amount) {

                    $resp = json_decode($response->getBody(), true);
                    $message = [];

                    foreach ($resp as $res) {
                        $change    = $res["quote"]["ytdChange"] + 1;
                        $newAmount = round($amount * $change,2);
                        $message   = "If you invested \${$amount} Jan 1st of this year, you'd have \${$newAmount} now.";
                    }

                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("What if? {$stonk}", $message, null, $newAmount > $amount ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);
                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("I died: ".$e->getMessage())
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }

        }

        if ($action[0] == ".buttahstonks") {

            $isLong = trim($action[1]??'');

            try {

                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols=ARLP,DBC,SACH&types=quote");
                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $isLong, $channel, $isLong) {

                    $portfolio = json_decode($response->getBody(), true);
                    $message   = [];
                    $peRatio = [];

                    foreach ($portfolio as $item => $data) {

                        $peRatio[] = $data["quote"]["peRatio"];
                        $open = \round($data["quote"]["previousClose"],2);
                        $now  = \round($data["quote"]["latestPrice"],2);
                        if ($now > $open) {
                            $percentage = \round((($now / $open)-1)*100, 2);
                            $total["up"][] = $percentage;
                        }
                        elseif ($now < $open) {
                            $percentage = \round((($open / $now)-1)*100, 2);
                            $total["down"][] = $percentage;
                            $percentage = "-" . $percentage;
                        }
                        elseif ($open == $now) {
                            $percentage = 0;
                        }

                        $message[] = strtoupper($item) . " ({$percentage}%)";
                    }

                    $move    = round((array_sum($total["up"]??[]) - array_sum($total["down"]??[])) / count($portfolio),2);
                    $peRatio = array_filter($peRatio);
                    $message =
                        "Total Change " . $move .
                        "% \n Average P/E Ratio:  " . round(array_sum($peRatio) / count($peRatio),2)."x" .
                        (!empty($isLong) ? ("\n" . implode(', ',$message)) : "");


                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("Today's Moves", $message, null, $move > 0 ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("WTF bro!")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }

        }

        if ($action[0] == ".dingdongstonks") {

            $isLong = trim($action[1]??'');

            try {

                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols=AKER,DARE,LTBR,NBEV,TWTR,TEAM,TWLO,VZ,VG,MSFT,CBT&types=quote");
                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $isLong, $channel, $isLong) {

                    $portfolio = json_decode($response->getBody(), true);
                    $message   = [];
                    $peRatio = [];

                    foreach ($portfolio as $item => $data) {

                        $peRatio[] = $data["quote"]["peRatio"];
                        $open = \round($data["quote"]["previousClose"],2);
                        $now  = \round($data["quote"]["latestPrice"],2);
                        if ($now > $open) {
                            $percentage = \round((($now / $open)-1)*100, 2);
                            $total["up"][] = $percentage;
                        }
                        elseif ($now < $open) {
                            $percentage = \round((($open / $now)-1)*100, 2);
                            $total["down"][] = $percentage;
                            $percentage = "-" . $percentage;
                        }
                        elseif ($open == $now) {
                            $percentage = 0;
                        }

                        $message[] = strtoupper($item) . " ({$percentage}%)";
                    }

                    $move    = round((array_sum($total["up"]??[]) - array_sum($total["down"]??[])) / count($portfolio),2);
                    $peRatio = array_filter($peRatio);
                    $message =
                        "Total Change " . $move .
                        "% \n Average P/E Ratio:  " . round(array_sum($peRatio) / count($peRatio),2)."x" .
                        (!empty($isLong) ? ("\n" . implode(', ',$message)) : "");


                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("Today's Moves", $message, null, $move > 0 ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("WTF bro!")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }

        }

        if ($action[0] == ".beanstonks") {

            $isLong = trim($action[1]??'');

            try {

                $request = new \GuzzleHttp\Psr7\Request('GET', "https://api.iextrading.com/1.0/stock/market/batch?symbols=QQQ,BRK.B,ATVI&types=quote");
                $promise = $httpClient->sendAsync($request)->then(function ($response) use ($client, $isLong, $channel, $isLong) {

                    $portfolio = json_decode($response->getBody(), true);
                    $message   = [];
                    $peRatio = [];

                    foreach ($portfolio as $item => $data) {

                        $peRatio[] = $data["quote"]["peRatio"];
                        $open = \round($data["quote"]["previousClose"],2);
                        $now  = \round($data["quote"]["latestPrice"],2);
                        if ($now > $open) {
                            $percentage = \round((($now / $open)-1)*100, 2);
                            $total["up"][] = $percentage;
                        }
                        elseif ($now < $open) {
                            $percentage = \round((($open / $now)-1)*100, 2);
                            $total["down"][] = $percentage;
                            $percentage = "-" . $percentage;
                        }
                        elseif ($open == $now) {
                            $percentage = 0;
                        }

                        $message[] = strtoupper($item) . " ({$percentage}%)";
                    }

                    $move    = round((array_sum($total["up"]??[]) - array_sum($total["down"]??[])) / count($portfolio),2);
                    $peRatio = array_filter($peRatio);
                    $message =
                        "Total Change " . $move .
                        "% \n Average P/E Ratio:  " . round(array_sum($peRatio) / count($peRatio),2)."x" .
                        (!empty($isLong) ? ("\n" . implode(', ',$message)) : "");


                    $message = $client->getMessageBuilder()
                        ->addAttachment(
                            new Attachment("Today's Moves", $message, null, $move > 0 ? "#00ff00" : "#ff0000")
                        )
                        ->setText('')
                        ->setChannel($channel)
                        ->create();
                    $client->postMessage($message);

                });
                $promise->wait();

            } catch (\Exception $e) {

                $message = $client->getMessageBuilder()
                    ->setText("WTF bro!")
                    ->setChannel($channel)
                    ->create();
                $client->postMessage($message);

            }

        }
        
    });
});

$loop->run();