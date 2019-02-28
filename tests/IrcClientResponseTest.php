<?php

namespace Tests;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Messages\IrcMessage;

class IrcClientResponseTest extends TestCase
{
    /**
     *  Make sure the client returns a PING request with an equal PONG response.
     */
    public function testPingPong()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendCommand'])
            ->getMock();
        $client->expects($this->once())
            ->method('sendCommand')
            ->with('PONG :0123456');

        $msg = new IrcMessage('PING :0123456');
        $this->callPrivate($client, 'handleIrcMessage', [$msg]);
    }

    /**
     *  `sendMessage` should generate a PRIVMSG command.
     */
    public function testSendMessage()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendCommand'])
            ->getMock();
        $client->expects($this->once())
            ->method('sendCommand')
            ->with('PRIVMSG #channel :Hello World!');

        $client->sendMessage('#channel', 'Hello World!');
    }
}
