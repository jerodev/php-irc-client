<?php

namespace Jerodev\PhpIrcClient\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcClient;

class IrcMessage
{
    /** @var string */
    private $rawMessage;

    /** @var string */
    protected $command;

    /** @var string */
    protected $commandsuffix;

    /** @var bool */
    protected $handled;

    /** @var string */
    protected $payload;

    /** @var string */
    protected $source;

    public function __construct(string $command)
    {
        $this->handled = false;
        $this->parse($command);
    }

    /**
     *  This function is always called after the message is parsed.
     *  The handle will only be executed once unless forced.
     *
     *  @param IrcClient $client A reference to the irc client object
     *  @param bool $force Force handling this message even if already handled.
     */
    public function handle(IrcClient $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }
    }

    /**
     *  Get the events that should be invoked for this message.
     *
     *  @return Event[]
     */
    public function getEvents()
    {
        return [];
    }

    /**
     *  Parse the irc command string to local properties.
     *
     *  @param string $command
     */
    private function parse(string $command): void
    {
        $command = trim($command);
        $this->rawMessage = $command;
        $i = 0;

        if ($command[0] === ':') {
            $i = strpos($command, ' ');
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
