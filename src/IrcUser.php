<?php

namespace Jerodev\PhpIrcClient;

class IrcUser 
{
    /** @var string */
    public $username;
    
    function __construct(string $username)
    {
        $this->username = $username;
    }
}