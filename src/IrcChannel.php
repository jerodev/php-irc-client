<?php

namespace Jerodev\PhpIrcClient;

class IrcChannel
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
