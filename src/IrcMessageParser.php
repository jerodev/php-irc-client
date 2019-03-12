<?php

namespace Jerodev\PhpIrcClient;

use Generator;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\KickMessage;
use Jerodev\PhpIrcClient\Messages\MOTDMessage;
use Jerodev\PhpIrcClient\Messages\NameReplyMessage;
use Jerodev\PhpIrcClient\Messages\PingMessage;
use Jerodev\PhpIrcClient\Messages\PrivmsgMessage;
use Jerodev\PhpIrcClient\Messages\TopicChangeMessage;
use Jerodev\PhpIrcClient\Messages\WelcomeMessage;

class IrcMessageParser
{
    /**
     *  Parse one ore more irc messages.
     *
     *  @param string $message A string received from the irc server
     *
     *  @return Generator|IrcMessage[]
     */
    public function parse(string $message)
    {
        foreach (explode("\n", $message) as $msg) {
            if (empty(trim($msg))) {
                continue;
            }

            yield $this->parseSingle($msg);
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
        switch ($this->getCommand($message)) {
            case 'KICK':
                $msg = new KickMessage($message);
                break;

            case 'PING':
                $msg = new PingMessage($message);
                break;

            case 'PRIVMSG':
                $msg = new PrivmsgMessage($message);
                break;

            case IrcCommand::RPL_WELCOME:
                $msg = new WelcomeMessage($message);
                break;

            case 'TOPIC':
            case IrcCommand::RPL_TOPIC:
                $msg = new TopicChangeMessage($message);
                break;

            case IrcCommand::RPL_NAMREPLY:
                $msg = new NameReplyMessage($message);
                break;

            case IrcCommand::RPL_MOTD:
                $msg = new MOTDMessage($message);
                break;

            default:
                $msg = new IrcMessage($message);
                break;
        }

        return $msg;
    }

    /**
     *  Get the COMMAND part of an irc message.
     *
     *  @param string $message a raw irc message
     *
     *  @return string
     */
    private function getCommand(string $message): string
    {
        if ($message[0] === ':') {
            $message = trim(strstr($message, ' '));
        }

        return strstr($message, ' ', true);
    }
}
