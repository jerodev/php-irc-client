<?php

declare(strict_types=1);

namespace Tests;

use Jerodev\PhpIrcClient\IrcMessageParser;
use Jerodev\PhpIrcClient\Messages\IrcMessage;
use Jerodev\PhpIrcClient\Messages\MOTDMessage;

final class IrcMessageParserTest extends TestCase
{
    protected IrcMessageParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = new IrcMessageParser();
    }

    public function testSingleLineMessage(): void
    {
        $message = ':*.freenode.net NOTICE * :*** Could not resolve your '
            . 'hostname: Domain not found; using your IP address '
            . '(xxx.xxx.xxx.xxx) instead.';
        $count = 0;
        foreach ($this->parser->parse($message) as $parsed) {
            $count++;
            self::assertInstanceOf(IrcMessage::class, $parsed);
        }
        self::assertSame(1, $count);
    }

    public function testMultipleLineMessage(): void
    {
        $message = ':*.freenode.net NOTICE * :*** Looking up your ident...'
            . \PHP_EOL
            . ':*.freenode.net NOTICE * :*** Looking up your hostname...';
        $count = 0;
        foreach ($this->parser->parse($message) as $parsed) {
            $count++;
            self::assertInstanceOf(IrcMessage::class, $parsed);
        }
        self::assertSame(2, $count);
    }

    public function testMotd(): void
    {
        $message = ':*.freenode.net 372 test-irc-bot :  Hello, World!' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :  Welcome to the' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :          __                               _' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :         / _|_ __ ___  ___ _ __   ___   __| | ___' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :        | |_| \'__/ _ \\/ _ \\ \'_ \\ / _ \\ / _` |/ _ \\' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :        |  _| | |  __/  __/ | | | (_) | (_| |  __/' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :        |_| |_|  \\___|\\___|_| |_|\\___/ \\__,_|\\___|' . \PHP_EOL
            . ':*.freenode.net 372 test-irc-bot :                                   AUTONOMOUS ZONE' . \PHP_EOL;
        $count = 0;
        foreach ($this->parser->parse($message) as $parsed) {
            $count++;
            self::assertInstanceOf(IrcMessage::class, $parsed);
        }
        self::assertSame(9, $count);
    }
}
