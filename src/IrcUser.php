<?php

namespace Jerodev\PhpIrcClient;

class IrcUser
{
    /** @var string */
    public $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }
}
