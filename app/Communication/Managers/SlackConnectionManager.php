<?php

namespace App\Communication\Managers;


use App\Commands\Command;
use App\Communication\ConnectionManager;
use App\Communication\Message;
use GuzzleHttp\Client;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Slack\Channel;
use Slack\RealTimeClient;

class SlackConnectionManager implements ConnectionManager
{


    /**
     * @var RealTimeClient
     */
    private $slackClient;

    protected $loop;

    /**
     * SlackConnectionManager constructor.
     * @param RealTimeClient $client
     * @param LoopInterface $loop
     */
    public function __construct(RealTimeClient $client, LoopInterface $loop)
    {
        $this->slackClient = $client;
        $this->loop = $loop;
        $this->registerMessageEvent();
    }

    private function registerMessageEvent()
    {
        // TODO(vulski): Command checking abstraction.... CommandRegistry?

        $this->slackClient->on('message', function ($data) {
            $this->slackClient->getChannelGroupOrDMByID($data['channel'])->then(function ($channel) use ($data) {
                /** @var Channel $channel */
                $message = new Message($channel->getName(), $data);
                $action = explode(" ", strtolower($data["text"])) ?? null;
                foreach (resolve('commands') as $command) {
                    /* @var Command $command */
                    if (is_array($command->command())) {
                        foreach ($command->command() as $alias) {
                            if ($action[0] == config('app')['token'] . $alias) {
                                $this->sendMessage($command->run($message));
                            }
                        }
                    } else {
                        if ($action[0] == config('app')['token'] . $command->command()) {
                            $this->sendMessage($command->run($message));
                        }
                    }
                }
            });
        });
    }

    /**
     * Connect to a server.
     * @return Promise
     */
    public function connect(): PromiseInterface
    {
        return $this->slackClient->connect();
    }

    /**
     * Disconnect from a server.
     */
    public function disconnect()
    {
        $this->loop->stop();
        $this->slackClient->disconnect();
    }

    /**
     * Send a message to a server and channel.
     * @param Message $msg
     * @return bool
     */
    public function sendMessage(Message $msg): bool
    {
        $this->slackClient->getChannelByName($msg->getChannel())->then(function ($channel) use ($msg){
            $message = $this->slackClient->getMessageBuilder()
                ->setText($msg->getMessage())
                ->setChannel($channel);
            foreach ($msg->getAttachments() as $attachment) {
                $message->addAttachment($attachment);
            }

            return $this->slackClient->postMessage($message->create());
        });
    }

    /**
     * Start the loop.
     */
    public function run()
    {
        $this->loop->run();
    }
}