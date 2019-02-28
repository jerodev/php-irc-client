<?php

namespace Jerodev\PhpIrcClient\Messages;

class TopicChangeMessage extends IrcMessage
{
    /** @var string */
    public $channel;

    /** @var string */
    public $topic;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = preg_replace('/^[^\#]+(\#.*?)$/', '$1', $this->commandsuffix);
        $this->topic = $this->payload;
    }
}
