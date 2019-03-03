<?php

namespace Jerodev\PhpIrcClient;

use Exception;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class IrcConnection
{
    /** @var bool */
    private $connected;

    /** @var ConnectionInterface|null */
    private $connection;

    /** @var EventHandlerCollection */
    private $eventHandlerCollection;

    /** @var LoopInterface */
    private $loop;

    /** @var IrcMessageParser */
    private $messageParser;

    /** @var string */
    private $server;

    public function __construct(string $server)
    {
        $this->connected = false;
        $this->eventHandlerCollection = new EventHandlerCollection();
        $this->messageParser = new IrcMessageParser();
        $this->server = $server;
    }

    /**
     *  Open a connection to the irc server.
     */
    public function open()
    {
        $this->loop = \React\EventLoop\Factory::create();
        $tcpConnector = new \React\Socket\TcpConnector($this->loop);
        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('1.1.1.1', $this->loop);
        $dnsConnector = new \React\Socket\DnsConnector($tcpConnector, $dns);

        $dnsConnector->connect($this->server)->then(function (ConnectionInterface $connection) {
            $this->connection = $connection;
            $this->connected = true;

            $this->connection->on('data', function ($data) {
                foreach ($this->messageParser->parse($data) as $msg) {
                    $this->handleMessage($msg);
                }
            });

            $this->connection->on('close', function () {
                $this->connected = false;
            });
            $this->connection->on('end', function () {
                $this->connected = false;
            });
        });

        $this->loop->run();
    }

    /**
     *  Close the current irc server connection.
     */
    public function close(): void
    {
        if ($this->isConnected()) {
            $this->connection->close();
            $this->loop->stop();
        }
    }

    /**
     *  Test if there is an open connection to the irc server.
     */
    public function isConnected(): bool
    {
        return $this->connection && $this->connected;
    }

    /**
     *  Set a callback for received irc data
     *  An IrcMessage object will be passed to the callback.
     *
     *  @param callable $function The function to be called.
     */
    public function onData(callable $function): void
    {
        $this->eventHandlerCollection->addHandler('data', $function);
    }

    /**
     * Send a command to the irc server.
     *
     *  @param string $command The raw irc command.
     *
     *  @throws Exception if no open connection is available.
     */
    public function write(string $command): void
    {
        if (!$this->isConnected()) {
            throw new Exception('No open connection was found to write commands to.');
        }

        // Make sure the command ends in a newline character
        if (substr($command, -1) !== "\n") {
            $command .= "\n";
        }

        $this->connection->write($command);
    }

    /**
     *  Handle a single parsed IrcMessage.
     *
     *  @param IrcMessage $message The message received from the server.
     */
    private function handleMessage(IrcMessage $message): void
    {
        $this->eventHandlerCollection->invoke('data', [$message]);
    }
}
