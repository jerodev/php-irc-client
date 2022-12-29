<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Options;

class ConnectionOptions
{
    public function __construct(public int $floodProtectionDelay = 0)
    {
    }
}
