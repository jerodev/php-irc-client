<?php

namespace Jerodev\PhpIrcClient\Options;

class ConnectionOptions
{
    /** @var int */
    public $floodProtectionDelay;

    public function __construct()
    {
        $this->floodProtectionDelay = 0;
    }
}