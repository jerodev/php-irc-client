<?php

namespace Jerodev\PhpIrcClient;

use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\NameReplyMessage;
use Jerodev\PhpIrcClient\Messages\TopicChangeMessage;

class IrcMessageParser
{
    /**
     *  Parse one ore more irc messages.
     *
     *  @param string $message A string received from the irc server
     *
     *  @return IrcMessage[]
     */
    public function parse(string $message)
    {
        if ($messages = preg_split('/\r?\n\r?/', $message, -1, PREG_SPLIT_NO_EMPTY)) {
            foreach ($messages as $msg) {
                yield $this->parseSingle($msg);
            }
        }
    }

    /**
     *  Parse a single message to a corresponding object.
     *
     *  @param string $message
     *
     *  @return IrcMessage
     */
    private function parseSingle(string $message): IrcMessage
    {
        $command = preg_replace('/^(?::[^\s]+\s+)?([^\s]+).*?$/', '$1', $message);
        switch ($command) {
            case IrcCommand::RPL_TOPIC:
                $msg = new TopicChangeMessage($message);
                break;

            case IrcCommand::RPL_NAMREPLY:
                $msg = new NameReplyMessage($message);
                break;

            default:
                $msg = new IrcMessage($message);
                break;
        }

        return $msg;
    }
}
