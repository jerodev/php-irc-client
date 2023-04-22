<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Jerodev\PhpIrcClient\IrcConnection;

final class IrcConnectionTest extends TestCase
{
    public function testIsConnectedNotConnected(): void
    {
        $connection = new IrcConnection('chat.example.com');
        self::assertFalse($connection->isConnected());
    }

    public function testGetServer(): void
    {
        $connection = new IrcConnection('chat.example.com');
        self::assertSame('chat.example.com', $connection->getServer());
    }

    public function testWriteWithoutConnection(): void
    {
        $connection = new IrcConnection('chat.example.com');
        self::expectException(Exception::class);
        self::expectExceptionMessage(
            'No open connection was found to write commands to.'
        );
        $connection->write('testing');
    }
}
