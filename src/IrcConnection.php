<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient;

use Exception;
use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Options\ConnectionOptions;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class IrcConnection
{
    private bool $connected = false;
    private ?ConnectionInterface $connection = null;
    private EventHandlerCollection $eventHandlerCollection;
    private bool $floodProtected;
    private LoopInterface $loop;
    private IrcMessageParser $messageParser;

    /** @var array<int, string> */
    private array $messageQueue = [];

    public function __construct(
        private string $server,
        ?ConnectionOptions $options = null
    ) {
        $options = $options ?? new ConnectionOptions();

        $this->eventHandlerCollection = new EventHandlerCollection();
        $this->floodProtected = $options->floodProtectionDelay > 0;
        $this->loop = \React\EventLoop\Factory::create();
        $this->messageParser = new IrcMessageParser();

        if ($this->floodProtected) {
            $this->loop->addPeriodicTimer($options->floodProtectionDelay / 1000, function () {
                if ($msg = array_shift($this->messageQueue)) {
                    $this->connection->write($msg);
                }
            });
        }
    }

    /**
     * Open a connection to the IRC server.
     */
    public function open(): void
    {
        if ($this->isConnected()) {
            return;
        }

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
     * Close the current IRC server connection.
     */
    public function close(): void
    {
        if ($this->isConnected()) {
            $this->connection->close();
            $this->loop->stop();
        }
    }

    /**
     * Test if there is an open connection to the IRC server.
     */
    public function isConnected(): bool
    {
        return $this->connection && $this->connected;
    }

    /**
     * Set a callback for received IRC data.
     * An IrcMessage object will be passed to the callback.
     * @param callable $function The function to be called.
     */
    public function onData(callable $function): void
    {
        $this->eventHandlerCollection->addHandler('data', $function);
    }

    /**
     * Send a command to the IRC server.
     * @param string $command The raw IRC command.
     * @throws Exception if no open connection is available.
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

        if ($this->floodProtected) {
            $this->messageQueue[] = $command;
        } else {
            $this->connection->write($command);
        }
    }

    /**
     * Handle a single parsed IrcMessage.
     */
    private function handleMessage(IrcMessage $message): void
    {
        $this->eventHandlerCollection->invoke(new Event('data', [$message]));
    }
}
