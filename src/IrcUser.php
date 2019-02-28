<?php

namespace Jerodev\PhpIrcClient;

class IrcUser
{
    /** @var string */
    public $nickname;

    public function __construct(string $nickname)
    {
        $this->nickname = $nickname;
    }
}
