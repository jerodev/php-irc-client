<?php

declare(strict_types=1);

namespace Tests;

use Jerodev\PhpIrcClient\IrcMessageParser;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\MOTDMessage;
use Jerodev\PhpIrcClient\Messages\NameReplyMessage;
use Jerodev\PhpIrcClient\Messages\PingMessage;
use Jerodev\PhpIrcClient\Messages\PrivmsgMessage;
use Jerodev\PhpIrcClient\Messages\TopicChangeMessage;

class IrcMessageTest extends TestCase
{
    public function testParseMultiple(): void
    {
        $msg = "PING :0123456\nPING :0123457";
        $commands = iterator_to_array((new IrcMessageParser())->parse($msg));

        $this->assertEquals([
            new PingMessage('PING :0123456'),
            new PingMessage('PING :0123457'),
        ], $commands);
    }

    public function testParseMotd(): void
    {
        $msg = new MOTDMessage(':Jerodev!~Jerodev@foo.bar.be 372 IrcBot :Message of the day');

        $this->assertSame('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertSame('372', $this->getPrivate($msg, 'command'));
        $this->assertSame('IrcBot', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertSame('Message of the day', $this->getPrivate($msg, 'payload'));
    }

    public function testParseNameReply(): void
    {
        $msg = new NameReplyMessage(':Jerodev!~Jerodev@foo.bar.be 353 IrcBot = #channel :IrcBot @Q OtherUser');

        $this->assertSame('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertSame('353', $this->getPrivate($msg, 'command'));
        $this->assertSame('IrcBot = #channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertSame('IrcBot @Q OtherUser', $this->getPrivate($msg, 'payload'));
        $this->assertSame('#channel', $msg->channel->getName());
        $this->assertSame(['IrcBot', '@Q', 'OtherUser'], $msg->names);
    }

    public function testParseTopicReply(): void
    {
        $msg = new IrcMessage(':Jerodev!~Jerodev@foo.bar.be TOPIC #channel :The newest channel topic!');

        $this->assertSame('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertSame('TOPIC', $this->getPrivate($msg, 'command'));
        $this->assertSame('#channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertSame('The newest channel topic!', $this->getPrivate($msg, 'payload'));
    }

    public function testParseTopicReplyNumeric(): void
    {
        $msg = new TopicChangeMessage(':Jerodev!~Jerodev@foo.bar.be 332 BotName #channel :The newest channel topic!!');

        $this->assertSame('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertSame('332', $this->getPrivate($msg, 'command'));
        $this->assertSame('#channel', $msg->channel->getName());
        $this->assertSame('The newest channel topic!!', $msg->topic);
    }

    public function testParseUserMessage(): void
    {
        $msg = new PrivmsgMessage(':Jerodev!~Jerodev@foo.bar.be PRIVMSG #channel :Hello World!');

        $this->assertSame('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertSame('Jerodev', $msg->user);
        $this->assertSame('PRIVMSG', $this->getPrivate($msg, 'command'));
        $this->assertSame('#channel', $msg->target);
        $this->assertSame('#channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertSame('Hello World!', $msg->message);
        $this->assertSame('Hello World!', $this->getPrivate($msg, 'payload'));
    }
}
