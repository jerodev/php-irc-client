<?php

namespace Tests;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcMessageParser;

class IrcClientResponseTest extends TestCase
{
    /**
     *  Test generating join/part commands.
     */
    public function testJoinPartChannel()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->setConstructorArgs([''])
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->exactly(2))
            ->method('send')
            ->withConsecutive(
                ['JOIN #php-irc-client-test'],
                ['PART #php-irc-client-test']
            );

        $client->join('#php-irc-client-test');
        $client->part('#php-irc-client-test');
    }

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

    /**
     *  `sendMessage` should generate multiple PRIVMSG commands for multiline messages.
     */
    public function testSendMultilineMessage()
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->exactly(2))
            ->method('send')
            ->withConsecutive(
                ['PRIVMSG #channel :Hello'],
                ['PRIVMSG #channel :World!']
            );

        $client->say('#channel', "Hello\nWorld!");
    }
}
