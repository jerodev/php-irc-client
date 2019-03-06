<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;

class PrivmsgMessage extends IrcMessage
{
    /** @var string */
    public $user;

    /** @var string */
    public $target;

    /** @var string */
    public $message;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->user = strstr($this->source, '!', true);
        $this->target = $this->commandsuffix;
        $this->message = $this->payload;
    }

    public function getEvents(): array
    {
        $events = [new Event('message', [$this->user, $this->target, $this->message])];

        if ($this->target[0] === '#') {
            $events[] = new Event("message$this->target", [$this->user, $this->message]);
        }
        
        return $events;
    }
}
