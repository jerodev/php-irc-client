<?php

namespace Jerodev\PhpIrcClient;

use Exception;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\Messages\IrcMessage;

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

    /** @var IrcUser|null */
    private $user;

    /**
     *  Create a new IrcClient instance.
     *
     *  @param string $server The server address to connect to including the port: `address:port`.
     *  @param null|string $username The username to use on the server. Can be set in more detail using `setUser()`.
     *  @param null|string|string[] $channels The channels to join on connect.
     */
    public function __construct(string $server, $username = null, $channels = null)
    {
        $this->connection = new IrcConnection($server);

        $this->user = $username === null ? null : new IrcUser($username);
        $this->channels = [];
        $this->messageEventHandlers = new EventHandlerCollection();

        if (!empty($channels)) {
            if (is_string($channels)) {
                $channels = [$channels];
            }

            foreach ($channels as $channel) {
                $this->channels[$channel] = new IrcChannel($channel);
            }
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
            $this->sendCommand("NICK :$user->nickname");
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
     *  Send a raw command string to the irc server.
     *
     *  @param string $command The full command string to send.
     */
    public function sendCommand(string $command): void
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
    public function sendMessage(string $target, string $message): void
    {
        $this->sendCommand("PRIVMSG $target :$message");
    }

    /**
     *  Grab channel information by its name.
     *  This function makes sure the channel exists on this client first.
     *
     *  @param string $name The name of this channel.
     *
     *  @return IrcChannel
     */
    public function getChannel(string $name): IrcChannel
    {
        if (($this->channels[$name] ?? null) === null) {
            $this->channels[$name] = new IrcChannel($name);
        }

        return $this->channels[$name];
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
     *  Take actions required for received irc messages and invoke the correct event handlers.
     *
     *  @param IrcMessage $message The message object for the received line.
     */
    private function handleIrcMessage(IrcMessage $message): void
    {
        $message->handle($this);

        if (!$this->isAuthenticated && $this->user) {
            $this->sendCommand("USER {$this->user->nickname} * * :{$this->user->nickname}");
            $this->sendCommand("NICK {$this->user->nickname}");
            $this->isAuthenticated = true;
        }

        //$this->messageEventHandlers->invoke($message->command, [$message]);
    }
}
