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
     * @var Attachment[]
     */
    private $attachments;

    /**
     * Message constructor.
     * @param string $channel
     * @param string $message
     * @param Attachment[] $attachments
     */
    public function __construct(string $channel, string $message, $attachments = [])
    {
        $this->channel = $channel;
        $this->message = $message;
        $this->attachments = $attachments;
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

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param Attachment[] $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }
}