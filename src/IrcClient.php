<?php

namespace Jerodev\PhpIrcClient;

use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class IrcClient
{
    const RPL_WELCOME = '001';

    /** @var IrcChannel[] */
    private $channels;

    /** @var ConnectionInterface */
    private $connection;

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

        if (!empty($channels)) {
            if (is_string($channels)) {
                $channels = [$channels];
            }

            foreach ($channels as $channel) {
                $this->channels[] = new IrcChannel($channel);
            }
        }
    }

    /**
     *  Connect to the irc server and start listening for messages.
     */
    public function connect(): void
    {
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
                foreach ($this->parseMessages($data) as $msg) {
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
    public function onMessage($event, ?callable $function = null)
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
     *  Take actions required for received irc messages and invoke the correct event handlers.
     *
     *  @param IrcMessage $message The message object for the received line.
     */
    private function handleIrcMessage(IrcMessage $message): void
    {
        //var_dump($message);

        switch ($message->command) {
            case 'PING':
                $this->sendCommand("PONG :$message->payload");
                break;

            case self::RPL_WELCOME:
                $this->sendCommand('JOIN #pokedextest');
                $this->sendMessage('#pokedextest', "A wild IrcBot appeared!");
                break;
        }

        if (!$this->isAuthenticated && $this->user) {
            $this->sendCommand("USER {$this->user->username} * * :{$this->user->username}");
            $this->sendCommand("NICK {$this->user->username}");
            $this->isAuthenticated = true;
        }
    }

    /**
     *  Parse one or more incomming irc messages.
     *
     *  @param string $message The raw message contents.
     *  .
     *  @return IrcMessage[] An array of parsed messages.
     */
    private function parseMessages(string $message)
    {
        if ($messages = preg_split('/\r?\n\r?/', $message, -1, PREG_SPLIT_NO_EMPTY)) {
            foreach ($messages as $msg) {
                yield new IrcMessage($msg);
            }
        }
    }
}
