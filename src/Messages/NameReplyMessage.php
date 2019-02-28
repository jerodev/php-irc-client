<?php

namespace Jerodev\PhpIrcClient\Messages;

class NameReplyMessage extends IrcMessage
{
    /** @var string */
    public $channel;

    /** @var string[] */
    public $names;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = preg_replace('/^[^\#]+(\#.*?)$/', '$1', $this->commandsuffix);
        $this->names = preg_split('/\s+/', $this->payload, -1, PREG_SPLIT_NO_EMPTY);
    }
}
