<?php

declare(strict_types=1);

namespace Tests;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcMessageParser;
use Jerodev\PhpIrcClient\Options\ClientOptions;

class IrcClientResponseTest extends TestCase
{
    /**
     * Test autojoining a channel after kick.
     */
    public function testAutoJoinAfterKick(): void
    {
        $options = new ClientOptions('PhpIrcBot', ['#php-irc-client-test']);
        $options->autoRejoin = true;

        $client = $this->getMockBuilder(IrcClient::class)
            ->setConstructorArgs(['', $options])
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->exactly(3))
            ->method('send')
            ->withConsecutive(
                ['JOIN #php-irc-client-test'],
                ['USER PhpIrcBot * * :PhpIrcBot'],
                ['NICK PhpIrcBot']
            );

        foreach ((new IrcMessageParser())->parse('KICK #php-irc-client-test PhpIrcBot') as $msg) {
            $this->callPrivate($client, 'handleIrcMessage', [$msg]);
        }
    }

    /**
     * Test generating join/part commands.
     */
    public function testJoinPartChannel(): void
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
     * If autojoin is off, the client should not auto rejoin after kick.
     */
    public function testNotAutoJoinAfterKick(): void
    {
        $client = $this->getMockBuilder(IrcClient::class)
            ->setConstructorArgs(['', new ClientOptions('PhpIrcBot', ['#php-irc-client-test'])])
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->exactly(2))
            ->method('send')
            ->withConsecutive(
                ['USER PhpIrcBot * * :PhpIrcBot'],
                ['NICK PhpIrcBot']
            );

        foreach ((new IrcMessageParser())->parse('KICK #php-irc-client-test PhpIrcBot') as $msg) {
            $this->callPrivate($client, 'handleIrcMessage', [$msg]);
        }
    }

    /**
     * Make sure the client returns a PING request with an equal PONG response.
     */
    public function testPingPong(): void
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
     * `sendMessage` should generate a PRIVMSG command.
     */
    public function testSendMessage(): void
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
     * `sendMessage` should generate multiple PRIVMSG commands for multiline
     * messages.
     */
    public function testSendMultilineMessage(): void
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
