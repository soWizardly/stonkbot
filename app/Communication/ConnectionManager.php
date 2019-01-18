<?php

namespace App\Communication;


use React\Promise\Promise;
use React\Promise\PromiseInterface;

interface ConnectionManager
{

    /**
     * Connect to a server.
     * @return PromiseInterface
     */
    public function connect(): PromiseInterface;

    /**
     * Disconnect from a server.
     */
    public function disconnect();

    /**
     * Send a message to a server and channel.
     * @param Message $msg
     * @return bool
     */
    public function sendMessage(Message $msg): bool;

    /**
     * Start the loop.
     */
    public function run();
}