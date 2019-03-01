<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\IrcClient;

class WelcomeMessage extends IrcMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
    
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
        
        $client->sendCommand('JOIN #pokedextest');
        $client->sendMessage('#pokedextest', 'A wild IrcBot appeared!');
    }
}
