<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

class InviteMessage extends IrcMessage
{
    /**
     * Channel the client was invited to.
     */
    public IrcChannel $channel;

    /**
     * Name of the channel client was was invited to.
     */
    public string $target;

    /**
     * Name of the user inviting the client to.
     */
    public string $user;

    public function __construct(string $command)
    {
        parent::__construct($command);
        [$this->user] = explode(' ', $command);
        [$this->user] = explode('!', $this->user);
        $this->user = substr($this->user, 1);
        $this->target = $this->payload;
        $this->channel = new IrcChannel($this->target);
    }

    public function handle(IrcClient $client, bool $force = false): void
    {
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [
            new Event('invite', [$this->channel, $this->user]),
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
