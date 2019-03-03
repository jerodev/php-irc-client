<?php

namespace Tests;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcMessageParser;

class IrcClientResponseTest extends TestCase
{
    /**
     *  Make sure the client returns a PING request with an equal PONG response.
     */
    public function testPingPong()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->setConstructorArgs([''])
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->once())
            ->method('send')
            ->with('PONG :0123456');

        foreach ((new IrcMessageParser())->parse('PING :0123456') as $msg) {
            $this->callPrivate($client, 'handleIrcMessage', [$msg]);
        }
    }

    /**
     *  `sendMessage` should generate a PRIVMSG command.
     */
    public function testSendMessage()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->once())
            ->method('send')
            ->with('PRIVMSG #channel :Hello World!');

        $client->say('#channel', 'Hello World!');
    }
}
