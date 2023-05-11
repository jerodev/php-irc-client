<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcClient;

class NameReplyMessage extends IrcMessage
{
    public string $channel;

    /** @var array<int, string> */
    public array $names;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandsuffix ?? '', '#');
        $this->names = explode(' ', $this->payload);
    }

    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        if (!empty($this->names)) {
            $client->getChannel($this->channel)->setUsers($this->names);
        }
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [
            new Event('names', [$this->channel, $this->names]),
            new Event("names$this->channel", [$this->names]),
        ];
    }
}
