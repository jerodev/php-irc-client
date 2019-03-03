<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\EventArgs;

class MOTDMessage extends IrcMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getEventArgs(): array
    {
        return [
            new EventArgs('motd', [$this->payload]),
        ];
    }
}
