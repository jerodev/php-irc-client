<?php

namespace Jerodev\PhpIrcClient;

use Exception;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\NameReplyMessage;
use Jerodev\PhpIrcClient\Messages\TopicChangeMessage;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class IrcClient
{
    /** @var IrcChannel[] */
    private $channels;

    /** @var ConnectionInterface|null */
    private $connection;

    /** @var IrcMessageParser */
    private $ircMessageParser;

    /**
     * Used to track if the username has been sent to the server.
     *
     * @var bool
     */
    private $isAuthenticated;

    /** @var LoopInterface */
    private $loop;

    /** @var EventHandlerCollection */
    private $messageEventHandlers;

    /** @var string */
    private $server;

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
        $this->server = $server;
        $this->user = $username === null ? null : new IrcUser($username);
        $this->channels = [];
        $this->ircMessageParser = new IrcMessageParser();
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
    public function setNick($user): void
    {
        if (is_string($user)) {
            $user = new IrcUser($user);
        }

        if ($this->isConnected() && $this->user->nickname !== $user->nickname) {
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

        if ($this->isConnected()) {
            return;
        }

        $this->isAuthenticated = false;

        $this->loop = \React\EventLoop\Factory::create();
        $tcpConnector = new \React\Socket\TcpConnector($this->loop);
        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('1.1.1.1', $this->loop);
        $dnsConnector = new \React\Socket\DnsConnector($tcpConnector, $dns);

        $dnsConnector->connect($this->server)->then(function (ConnectionInterface $connection) {
            $this->connection = $connection;

            $this->connection->on('data', function ($data) {
                foreach ($this->ircMessageParser->parse($data) as $msg) {
                    $this->handleIrcMessage($msg);
                }
            });
        });

        $this->loop->run();
    }

    /**
     *  Close the current connection, if any.
     */
    public function disconnect(): void
    {
        if ($this->isConnected()) {
            $this->connection->close();
            $this->loop->stop();
            
            $this->connection = null;
        }
    }

    /**
     *  Test wether a connection is currently open for this client.
     *
     *  @return bool
     */
    public function isConnected(): bool
    {
        return $this->connection !== null;
    }

    /**
     *  Register an event handler for irc messages.
     *
     *  @param callable|string $event The name of the event to listen for. Pass a callable to this parameter to catch all events.
     *  @param callable|null $function The callable that will be invoked on event.
     */
    public function addMessageHandler($event, ?callable $function = null)
    {
        $this->messageEventHandlers->addHandler($event, $function);
    }

    /**
     *  Send a raw command string to the irc server.
     *
     *  @param string $command The full command string to send.
     */
    public function sendCommand(string $command): void
    {
        // Make sure the command ends in a newline character
        if (substr($command, -1) !== "\n") {
            $command .= "\n";
        }

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
        
        $this->messageEventHandlers->invoke($message->command, [$message]);
    }
}
