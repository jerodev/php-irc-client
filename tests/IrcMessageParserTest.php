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

    public function testMOTD(): void
    {
    }
}
