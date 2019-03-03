<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcClient;

class TopicChangeMessage extends IrcMessage
{
    /** @var string */
    public $channel;

    /** @var string */
    public $topic;

    /** @var string */
    public $user;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = preg_replace('/^[^\#]*(\#.*?)$/', '$1', $this->commandsuffix);
        $this->topic = $this->payload;
    }

    /**
     *  Change the topic for the referenced channel.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $client->getChannel($this->channel)->setTopic($this->topic);
    }

    public function getEvents(): array
    {
        return [
            new Event('topic', [$this->channel, $this->topic])
        ];
    }
}
