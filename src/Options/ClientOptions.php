<?php

namespace Jerodev\PhpIrcClient\Options;

class ClientOptions
{
    /** @var string[] */
    public $channels;

    /** @var int */
    public $floodProtectionDelay;

    /** @var string */
    public $nickname;

    /**
     *  @param string $nickname The nickname used on the irc server.
     *  @param string|string[] $channels The channels to join on connection.
     */
    public function __construct(string $nickname = null, $channels = [])
    {
        $this->nickname = $nickname;

        if (!is_array($channels)) {
            $channels = [$channels];
        }
        $this->channels = $channels;
    }

    /**
     *  Get the options for the IrcConnection from this collection.
     *
     *  @return ConnectionOptions
     */
    public function connectionOptions(): ConnectionOptions
    {
        $options = new ConnectionOptions();
        $options->floodProtectionDelay = $this->floodProtectionDelay;

        return $options;
    }
}
