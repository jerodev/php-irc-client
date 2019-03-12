<?php

namespace Jerodev\PhpIrcClient\Options;

class ClientOptions
{
    /**
     *  Automaticly connect to the irc server when creating the client.
     *
     *  @var bool
     */
    public $autoConnect;

    /**
     *  Automaticly rejoin a channel when kicked.
     *
     *  @var bool
     */
    public $autoRejoin;

    /**
     *  The channel names to join when connecting.
     *
     *  @var string[]
     */
    public $channels;

    /**
     *  The amount of time in milliseconds to wait between sending messages to the irc server.
     *
     *  @var int
     */
    public $floodProtectionDelay;

    /**
     *  The nickname to use on the server.
     *
     *  @var null|string
     */
    public $nickname;

    /**
     *  @param string $nickname The nickname used on the irc server.
     *  @param string|string[] $channels The channels to join on connection.
     */
    public function __construct(?string $nickname = null, $channels = [])
    {
        $this->nickname = $nickname;

        if (!is_array($channels)) {
            $channels = [$channels];
        }
        $this->channels = $channels;

        $this->autoRejoin = false;
        $this->autoConnect = false;
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
