<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\IrcClient;

class NameReplyMessage extends IrcMessage
{
    /** @var string */
    public $channel;

    /** @var string[] */
    public $names;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandsuffix, '#');
        $this->names = explode(' ', $this->payload);
    }
    
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
        
        if ($this->names) {
            $client->getChannel($this->channel)->setUsers($this->names);
        }
    }
}
