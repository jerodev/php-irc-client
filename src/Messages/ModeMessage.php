<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

class ModeMessage extends IrcMessage
{
    public ?IrcChannel $channel = null;
    public string $message;
    public string $mode;
    public ?string $target = null;
    public string $user;

    public function __construct(string $command)
    {
        parent::__construct($command);
        if ('#' === $this->commandsuffix[0]) {
            [$this->target, $this->mode] = explode(' ', $this->commandsuffix);
            $this->user = $this->payload;
        } else {
            $this->user = $this->commandsuffix;
            $this->mode = $this->payload;
        }
        $this->message = $this->payload;
    }

    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [
            new Event('mode', [$this->channel, $this->user, $this->mode]),
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
