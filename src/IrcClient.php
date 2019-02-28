<?php

namespace Jerodev\PhpIrcClient;

use React\EventLoop\LoopInterface;

class IrcClient
{
    /** @var IrcChannel[] */
    private $channels;
    
    /** @var LoopInterface */
    private $loop;
    
    /** @var string */
    private $server;
    
    /**
     *  Create a new IrcClient instance
     *
     *  @param string $server The server address to connect to including the port: `address:port`
     *  @param null|string|string[] $channels The channels to join on connect
     */
    function __construct(string $server, $channels = null)
    {
        $this->server = $server;
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
     *  Connect to the irc server and start listening for messages
     */
    public function connect(): void
    {
        $loop = \React\EventLoop\Factory::create();
        $tcpConnector = new \React\Socket\TcpConnector($loop);
        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dns = $dnsResolverFactory->createCached('1.1.1.1', $loop);
        $dnsConnector = new \React\Socket\DnsConnector($tcpConnector, $dns);

        $dnsConnector->connect($this->server)->then(function (\React\Socket\ConnectionInterface $connection) {
            $connection->on('data', function ($data) {
                foreach ($this->parseMessages($data) as $msg) {
                    var_dump($msg);
                }
            });
        });

        $loop->run();
    }
    
    /**
     *  Parse one or more incomming irc messages
     *
     *  @param string $message The raw message contents
     *  
     *  @return IrcMessage[] An array of parsed messages
     */
    private function parseMessages(string $message)
    {
        $messages = preg_split('/\r?\n\r?/', $message, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($messages as $msg) {
            yield new IrcMessage($msg);
        }
    }
}