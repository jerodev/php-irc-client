<?php

namespace Jerodev\PhpIrcClient;

class IrcMessage
{
    /** @var string */
    private $rawMessage;
    
    /** @var string */
    public $command;
    
    /** @var string */
    public $source;
    
    /** @var string */
    public $payload;
    
    /** @var string */
    public $server;
    
    function __construct(string $message)
    {
        $this->rawMessage = $message;
        
        if (preg_match('/^(?:(?<server>:[^\s]+)\s*)?(?<command>[^\s]+) (?<source>[^\s]+) (?<payload>.*?)$/', $message, $matches)) {
            $this->server = $matches['server'] ?? null;
            $this->command = $matches['command'] ?? null;
            $this->source = $matches['source'] ?? null;
            $this->payload = $matches['payload'] ?? null;
        }
    }
}