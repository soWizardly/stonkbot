<?php


namespace App\Commands\APIs;


use App\Commands\Command;
use App\Communication\Message;
use GuzzleHttp\Client;
use Slack\ChannelInterface;

class UrbanDictionaryCommand extends Command
{
    private $lastResult = [];
    private $i = 0;

    /**
     * The name of the command, or an array of aliases
     * Returning null means it has no command, and just listens to the channel.
     * @return string|array|null
     */
    public function command()
    {
        return ['d', 'define', 'whatis', 'another'];
    }

    /**
     * Run the command on the specified channel.
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $msg = explode(' ', $message->getMessage());
        if ($msg[0] == '.another') {
            $this->i++;
        } else {
            $this->i = 0;
            $api = "http://api.urbandictionary.com/v0/define?term=";
            $client = new Client();
            array_shift($msg);
            $result = $client->get($api . implode('', $msg));
            $this->lastResult = json_decode((string)$result->getBody(), true)['list'];
        }


        if (empty($this->lastResult)) {
            $definition = 'Never heard of it.';
        } else {
            if (isset($this->lastResult[$this->i])) {
                $definition = $this->lastResult[$this->i]['definition'];
            } else {
                $definition = 'no more, chump';
            }
        }

        if (strlen($definition) > 500) {
            $definition = substr($definition, 0, 500);
            $definition .= " ... ";
        }

        return new Message($message->getChannel(), $definition);
    }

    public function description(): string
    {
        return 'It\'s like Websters for hipsters';
    }
}