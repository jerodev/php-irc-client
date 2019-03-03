<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;

class MOTDMessage extends IrcMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getEvents(): array
    {
        return [
            new Event('motd', [$this->payload])
        ];
    }
}
