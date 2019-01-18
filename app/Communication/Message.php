<?php

namespace App\Communication;


class Message
{

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $message;

    /**
     * Message constructor.
     * @param string $channel
     * @param string $message
     */
    public function __construct(string $channel, string $message)
    {
        $this->channel = $channel;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }
}