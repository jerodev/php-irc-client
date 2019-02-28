<?php

namespace Tests;

use Jerodev\PhpIrcClient\IrcMessage;
use PHPUnit\Framework\TestCase;

class IrcMessageTest extends TestCase
{
    function testParseUserMessage()
    {
        $msg = new IrcMessage(':Jerodev!~Jerodev@foo.bar.be PRIVMSG #channel :Hello World!');
        
        $this->assertEquals('Jerodev!~Jerodev@foo.bar.be', $msg->source);
        $this->assertEquals('PRIVMSG', $msg->command);
        $this->assertEquals('#channel', $msg->commandsuffix);
        $this->assertEquals('Hello World!', $msg->payload);
    }
}