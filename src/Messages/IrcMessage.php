<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

class IrcMessage
{
    public ?IrcChannel $channel = null;
    protected ?string $commandsuffix = null;
    protected bool $handled = false;
    protected string $payload = '';
    protected ?string $source = null;
    public ?string $target = null;

    public function __construct(protected string $command)
    {
        $this->parse($this->command);
    }

    /**
     * This function is always called after the message is parsed.
     * The handle will only be executed once unless forced.
     *
     * @param IrcClient $client A reference to the irc client object
     * @param bool $force Force handling this message even if already handled
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
    }

    /**
     * Get the events that should be invoked for this message.
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [];
    }

    /**
     * Inject the list of IRC channels.
     * The messages can use this to gather information of the channel if needed.
     * @param array<string, IrcChannel> $channels
     */
    public function injectChannel(array $channels): void
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target];
        }
    }

    /**
     * Parse the IRC command string to local properties.
     */
    protected function parse(string $command): void
    {
        $command = trim($command);
        $i = 0;

        if ($command[0] === ':' && false !== strpos($command, ' ')) {
            $i = (int)strpos($command, ' ');
            $this->source = substr($command, 1, $i - 1);

            $i++;
        }

        $j = strpos($command, ' ', $i);
        if ($j !== false) {
            $this->command = substr($command, $i, $j - $i);
        } else {
            $this->command = substr($command, $i);

            return;
        }

        $i = strpos($command, ':', $j);
        if ($i !== false) {
            if ($i !== $j + 1) {
                $this->commandsuffix = substr($command, $j + 1, $i - $j - 2);
            }
            $this->payload = substr($command, $i + 1);
        } else {
            $this->commandsuffix = substr($command, $j + 1);
        }
    }
}
