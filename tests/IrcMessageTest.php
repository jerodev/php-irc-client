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

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertEquals('372', $this->getPrivate($msg, 'command'));
        $this->assertEquals('IrcBot', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertEquals('Message of the day', $this->getPrivate($msg, 'payload'));
    }

    public function testParseNameReply(): void
    {
        $msg = new NameReplyMessage(':Jerodev!~Jerodev@foo.bar.be 353 IrcBot = #channel :IrcBot @Q OtherUser');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertEquals('353', $this->getPrivate($msg, 'command'));
        $this->assertEquals('IrcBot = #channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertEquals('IrcBot @Q OtherUser', $this->getPrivate($msg, 'payload'));
        $this->assertEquals('#channel', $msg->channel);
        $this->assertEquals(['IrcBot', '@Q', 'OtherUser'], $msg->names);
    }

    public function testParseTopicReply(): void
    {
        $msg = new IrcMessage(':Jerodev!~Jerodev@foo.bar.be TOPIC #channel :The newest channel topic!');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertEquals('TOPIC', $this->getPrivate($msg, 'command'));
        $this->assertEquals('#channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertEquals('The newest channel topic!', $this->getPrivate($msg, 'payload'));
    }

    public function testParseTopicReplyNumeric(): void
    {
        $msg = new TopicChangeMessage(':Jerodev!~Jerodev@foo.bar.be 332 BotName #channel :The newest channel topic!!');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertEquals('332', $this->getPrivate($msg, 'command'));
        $this->assertEquals('#channel', $msg->channel);
        $this->assertEquals('The newest channel topic!!', $msg->topic);
    }

    public function testParseUserMessage(): void
    {
        $msg = new PrivmsgMessage(':Jerodev!~Jerodev@foo.bar.be PRIVMSG #channel :Hello World!');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $this->getPrivate($msg, 'source'));
        $this->assertEquals('Jerodev', $msg->user);
        $this->assertEquals('PRIVMSG', $this->getPrivate($msg, 'command'));
        $this->assertEquals('#channel', $msg->target);
        $this->assertEquals('#channel', $this->getPrivate($msg, 'commandsuffix'));
        $this->assertEquals('Hello World!', $msg->message);
        $this->assertEquals('Hello World!', $this->getPrivate($msg, 'payload'));
    }
}
