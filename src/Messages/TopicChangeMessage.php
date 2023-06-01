<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

class TopicChangeMessage extends IrcMessage
{
    public string $topic;

    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->channel = new IrcChannel(strstr($this->commandsuffix ?? '', '#'));
        $this->topic = $this->payload;
    }

    /**
     * Change the topic for the referenced channel.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $client->getChannel($this->channel->getName())->setTopic($this->topic);
    }

    /**
     * @return array<int, Event>
     */
    public function getEvents(): array
    {
        return [
            new Event('topic', [$this->channel, $this->topic]),
        ];
    }
}
