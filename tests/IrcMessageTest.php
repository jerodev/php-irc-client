<?php

namespace Tests;

use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\NameReplyMessage;
use PHPUnit\Framework\TestCase;

class IrcMessageTest extends TestCase
{
    public function testParseNameReply()
    {
        $msg = new NameReplyMessage(':Jerodev!~Jerodev@foo.bar.be 353 IrcBot = #channel :IrcBot @Q OtherUser');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $msg->source);
        $this->assertEquals('353', $msg->command);
        $this->assertEquals('IrcBot = #channel', $msg->commandsuffix);
        $this->assertEquals('IrcBot @Q OtherUser', $msg->payload);
        $this->assertEquals('#channel', $msg->channel);
        $this->assertEquals(['IrcBot', '@Q', 'OtherUser'], $msg->names);
    }

    public function testParseUserMessage()
    {
        $msg = new IrcMessage(':Jerodev!~Jerodev@foo.bar.be PRIVMSG #channel :Hello World!');

        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $msg->source);
        $this->assertEquals('PRIVMSG', $msg->command);
        $this->assertEquals('#channel', $msg->commandsuffix);
        $this->assertEquals('Hello World!', $msg->payload);
    }
}
