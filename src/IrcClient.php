<?php

namespace Jerodev\PhpIrcClient;

class IrcClient
{
    /** @var IrcChannel[] */
    private $channels;
    
    /** @var string */
    private $server;
    
    /**
     *  Create a new IrcClient instance
     *
     *  @param string $server The server address to connect to
     *  @param null|string|string[] $channels The channels to join on connect
     */
    function __construct(string $server, $channels = null)
    {
        $this->server = $server;
        
        if (!empty($channels)) {
            if (is_string($channels)) {
                $channels = [$channels];
            }
            
            foreach ($channels as $channel) {
                $this->channels = new IrcChannel($channel);
            }
        }
    }
}