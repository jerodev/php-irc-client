<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;

class PrivmsgMessage extends IrcMessage
{
    /** @var IrcChannel */
    public $channel;

    /** @var string */
    public $message;

    /** @var string */
    public $target;

    /** @var string */
    public $user;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->user = strstr($this->source, '!', true);
        $this->target = $this->commandsuffix;
        $this->message = $this->payload;
    }

    public function getEvents(): array
    {
        $events = [];
        if ($this->target[0] === '#') {
            $events = [
                new Event('message', [$this->user, $this->channel, $this->message]),
                new Event("message$this->target", [$this->user, $this->channel, $this->message]),
            ];
        } else {
            $events = [
                new Event('privmsg', [$this->user, $this->target, $this->message]),
            ];
        }

        return $events;
    }

    public function injectChannel(array $channels): void
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target];
        }
    }
}
