<?php

namespace Tests;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcConnection;
use Jerodev\PhpIrcClient\IrcMessageParser;

class IrcMessageEventTest extends TestCase
{
    public function testKick()
    {
        $this->invokeClientEvents(':Jerodev!~Jerodev@foo.bar.be KICK #channel user :Get out!', [new Event('kick', [new IrcChannel('#channel'), 'user', 'Get out!'])]);
    }

    public function testMOTD()
    {
        $this->invokeClientEvents(':Jerodev!~Jerodev@foo.bar.be 372 IrcBot :Message of the day', [new Event('motd', ['Message of the day'])]);
    }

    public function testNamesEvent()
    {
        $this->invokeClientEvents(
            ':Jerodev!~Jerodev@foo.bar.be 353 IrcBot = #channel :IrcBot @Q OtherUser',
            [[new Event('names', ['#channel', ['IrcBot', '@Q', 'OtherUser']])], [new Event('names#channel', [['IrcBot', '@Q', 'OtherUser']])]]
        );
    }

    public function testPingEvent()
    {
        $this->invokeClientEvents('PING :0123456', [new Event('ping')]);
        $this->invokeClientEvents("PING :0123456\nPING :0123457", [[new Event('ping')], [new Event('ping')]]);
    }

    public function testPrivmsgEvent()
    {
        $this->invokeClientEvents(
            ':Jerodev!~Jerodev@foo.bar.be PRIVMSG #channel :Hello World!',
            [
                [new Event('message', ['Jerodev', new IrcChannel('#channel'), 'Hello World!'])],
                [new Event('message#channel', ['Jerodev', new IrcChannel('#channel'), 'Hello World!'])],
            ]
        );
    }

    public function testTopicChangeEvent()
    {
        $this->invokeClientEvents(':Jerodev!~Jerodev@foo.bar.be TOPIC #channel :My Topic', [new Event('topic', ['#channel', 'My Topic'])]);
    }

    private function invokeClientEvents(string $message, array $expectedEvents): void
    {
        $eventCollection = $this->getMockBuilder(EventHandlerCollection::class)
            ->setMethods(['invoke'])
            ->getMock();
        $eventCollection->expects($this->exactly(count($expectedEvents)))
            ->method('invoke')
            ->withConsecutive(...$expectedEvents);

        $connection = $this->getMockBuilder(IrcConnection::class)
            ->setConstructorArgs([''])
            ->setMethods(['write'])
            ->getMock();

        $client = new IrcClient('');
        $client->setUser('PhpIrcClient');
        $this->setPrivate($client, 'messageEventHandlers', $eventCollection);
        $this->setPrivate($client, 'connection', $connection);
        $this->setPrivate($client, 'channels', ['#channel' => new IrcChannel('#channel')]);

        foreach ((new IrcMessageParser())->parse($message) as $msg) {
            $this->callPrivate($client, 'handleIrcMessage', [$msg]);
        }
    }
}
