<?php

namespace Jerodev\PhpIrcClient;

use Exception;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Options\ClientOptions;

class IrcClient
{
    /** @var IrcChannel[] */
    private $channels;

    /** @var IrcConnection */
    private $connection;

    /** @var bool */
    private $isAuthenticated;

    /** @var EventHandlerCollection */
    private $messageEventHandlers;

    /** @var ClientOptions */
    private $options;

    /** @var IrcUser|null */
    private $user;

    /**
     *  Create a new IrcClient instance.
     *
     *  @param string $server The server address to connect to including the port: `address:port`.
     *  @param ClientOptions $options An object depicting options for this connection.
     */
    public function __construct(string $server, ?ClientOptions $options = null, IrcConnection $connection = null)
    {
        $this->options = $options ?? new ClientOptions();
        $this->connection = $connection ?? new IrcConnection($server, $this->options->connectionOptions());

        $this->user = $this->options->nickname === null ? null : new IrcUser($this->options->nickname);
        $this->channels = [];
        $this->messageEventHandlers = new EventHandlerCollection();

        if (!empty($this->options->channels)) {
            foreach ($this->options->channels as $channel) {
                $this->channels[$channel] = new IrcChannel($channel);
            }
        }

        if ($this->options->autoConnect) {
            $this->connect();
        }
    }

    /**
     *  Set the user credentials for the connections.
     *  When a connection is already open, this function can be used to change the nickname of the client.
     *
     *  @param IrcUser|string $user The user information.
     */
    public function setUser($user): void
    {
        if (is_string($user)) {
            $user = new IrcUser($user);
        }

        if ($this->connection->isConnected() && $this->user->nickname !== $user->nickname) {
            $this->send("NICK :$user->nickname");
        }

        $this->user = $user;
    }

    /**
     *  Connect to the irc server and start listening for messages.
     *
     *  @throws Exception if no user information is provided before connecting.
     */
    public function connect(): void
    {
        if (!$this->user) {
            throw new Exception('A nickname must be set before connecting to an irc server.');
        }

        if ($this->connection->isConnected()) {
            return;
        }

        $this->isAuthenticated = false;
        $this->connection->onData(function ($msg) {
            $this->handleIrcMessage($msg);
        });
        $this->connection->open();
    }

    /**
     *  Close the current connection, if any.
     */
    public function disconnect(): void
    {
        $this->connection->close();
    }

    /**
     *  Register to an event callback.
     *
     *  @param string $event The event to register to.
     *  @param callable $callback The callback to be execute when the event is emitted.
     */
    public function on(string $event, callable $callback): void
    {
        $this->messageEventHandlers->addHandler($event, $callback);
    }

    /**
     *  Send a raw command string to the irc server.
     *
     *  @param string $command The full command string to send.
     */
    public function send(string $command): void
    {
        $this->connection->write($command);
    }

    /**
     *  Send a message to a channel or user.
     *  To send to a channel, make sure the `$target` starts with a `#`.
     *
     *  @param string $target The channel or user to message.
     *  @param string $message The message to send.
     */
    public function say(string $target, string $message): void
    {
        foreach (explode("\n", $message) as $msg) {
            $this->send("PRIVMSG $target :" . trim($msg));
        }
    }

    /**
     *  Join an irc channel.
     *
     *  @param string $channel The name of the channel to join.
     */
    public function join(string $channel): void
    {
        $channel = $this->channelName($channel);
        $this->send("JOIN $channel");
        $this->getChannel($channel);
    }

    /**
     *  Part from an irc channel.
     *
     *  @param string $channel The name of the channel to leave.
     */
    public function part(string $channel): void
    {
        $channel = $this->channelName($channel);

        if (array_key_exists($channel, $this->channels)) {
            $this->send("PART $channel");
        }
    }

    /**
     *  Grab channel information by its name.
     *  This function makes sure the channel exists on this client first.
     *
     *  @param string $channel The name of this channel.
     *
     *  @return IrcChannel
     */
    public function getChannel(string $channel): IrcChannel
    {
        $channel = $this->channelName($channel);

        if (($this->channels[$channel] ?? null) === null) {
            $this->channels[$channel] = new IrcChannel($channel);
        }

        return $this->channels[$channel];
    }

    /**
     *  Get the name with which the client is currently known on the server.
     *
     *  @var string
     */
    public function getNickname(): string
    {
        return $this->user->nickname;
    }

    /**
     *  Return a list of all channels.
     *
     *  @return IrcChannel[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     *  Indicates whether the client should autorejoin channels when kicked.
     *
     *  @return bool
     */
    public function shouldAutoRejoin(): bool
    {
        return $this->options->autoRejoin;
    }

    /**
     *  Take actions required for received irc messages and invoke the correct event handlers.
     *
     *  @param IrcMessage $message The message object for the received line.
     */
    private function handleIrcMessage(IrcMessage $message): void
    {
        $message->injectChannel($this->channels);
        $message->handle($this);

        if (!$this->isAuthenticated && $this->user) {
            $this->send("USER {$this->user->nickname} * * :{$this->user->nickname}");
            $this->send("NICK {$this->user->nickname}");
            $this->isAuthenticated = true;
        }

        foreach ($message->getEvents() as $event) {
            $this->messageEventHandlers->invoke($event);
        }
    }

    /**
     *  Make sure all channel names have the same format.
     *
     *  @param string $channel The name of the channel to format.
     *
     *  @return string The formatted name.
     */
    private function channelName(string $channel): string
    {
        if ($channel[0] !== '#') {
            $channel = "#$channel";
        }

        return $channel;
    }
}
