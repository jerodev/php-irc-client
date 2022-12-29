<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient;

class IrcUser
{
    public function __construct(public string $nickname)
    {
    }

    public function __toString(): string
    {
        return $this->nickname;
    }
}
