<?php


namespace App\Commands;


use App\Communication\Message;
use Slack\ChannelInterface;
use Slack\Message\Attachment;

class MillennialCapitalizationCommand extends Command
{

    /**
     * The name of the command, or an array of aliases
     * @return string|array
     */
    public function command()
    {
        return ['mc', 'tc'];
    }

    /**
     * Run the command on the specified channel.
     * @param Message $message The text the user said, exploded by space.
     * @return mixed
     */
    public function run(Message $message): Message
    {
        $msg = explode(" ", $message->getMessage());
        array_shift($msg);
        $msg = str_split(implode(' ', $msg));

        for ($i = 0; $i < count($msg); $i++) {
            if ($i % 2 >= 1) {
                $msg[$i] = strtoupper($msg[$i]);
            }
        }
        $message->setMessage(implode($msg));
        return $message;
    }

    public function description(): string
    {
        return 'Proper capitalization of the current generation.';
    }
}