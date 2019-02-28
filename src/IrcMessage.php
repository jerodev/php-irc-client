<?php

namespace Jerodev\PhpIrcClient;

class IrcMessage
{
    /** @var string */
    private $rawMessage;
    
    /** @var string */
    public $command;
    
    /** @var string */
    public $commandsuffix;
    
    /** @var string */
    public $payload;
    
    /** @var string */
    public $source;
    
    function __construct(string $message)
    {
        $this->rawMessage = $message;
        
        if (preg_match('/^(?::(?<source>[^\s]+)\s*)?(?<command>[^\s]+)\s*(?<commandsuffix>[^:$]+)?\s*(?::(?<payload>.*?))?$/', $message, $matches)) {
            $this->source = $matches['source'] ?? null;
            $this->command = $matches['command'] ?? null;
            $this->commandsuffix = trim($matches['commandsuffix'] ?? null);
            $this->payload = $matches['payload'] ?? null;
        }
    }
    
    /**
     *  Get the raw message line
     *
     *  @return string
     */
    public function getRaw(): string
    {
        return $this->rawMessage;
    }
}