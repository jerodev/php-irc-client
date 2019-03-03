<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\IrcClient;

class PingMessage extends IrcMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     *  Reply the ping message with a pong response.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $client->sendCommand("PONG :$this->payload");
    }
}
