<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;

class PrivmsgMessage extends IrcMessage
{
    public ?IrcChannel $channel = null;
    public string $message;
    public string $target;
    public string $user;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->user = strstr($this->source ?? '', '!', true);
        $this->target = (string)$this->commandsuffix;
        $this->message = $this->payload;
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        if ($this->target[0] === '#') {
            return [
                new Event('message', [$this->user, $this->channel, $this->message]),
                new Event("message$this->target", [$this->user, $this->channel, $this->message]),
            ];
        }
        return [
            new Event('privmsg', [$this->user, $this->target, $this->message]),
        ];
    }

    /**
     * @param array<string, IrcChannel> $channels
     */
    public function injectChannel(array $channels): void
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target];
        }
    }
}
