<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\IrcClient;

class WelcomeMessage extends IrcMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     *  On welcome message, join the selected channels.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        foreach ($client->getChannels() as $channel) {
            $client->sendCommand("JOIN {$channel->getName()}");
        }
    }
}
