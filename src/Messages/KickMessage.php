<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

class KickMessage extends IrcMessage
{
    public ?IrcChannel $channel = null;
    public string $message;
    private string $target;
    public string $user;

    public function __construct(string $message)
    {
        parent::__construct($message);

        [$this->target, $this->user] = explode(' ', $this->commandsuffix ?? '');
        $this->message = $this->payload;
    }

    /**
     * When the bot is kicked form a channel, it might need to auto-rejoin.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        if ($client->getNickname() === $this->user && $client->shouldAutoRejoin()) {
            $client->join($this->target);
        }
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [
            new Event('kick', [$this->channel, $this->user, $this->message]),
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
