<?php

namespace Jerodev\PhpIrcClient;

class IrcMessage
{
    /** @var string */
    private $rawMessage;
    
    function __construct(string $message)
    {
        $this->rawMessage = $message;
    }
}