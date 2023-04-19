<?php

declare(strict_types=1);

namespace Tests;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;

final class IrcClientTest extends TestCase
{
    public function testGetConnection(): void
    {
        $options = new ClientOptions('PhpIrcBot', ['#php-irc-client-test']);
        $client = new IrcClient('chat.example.com:6667', $options);

        $connection = $client->getConnection();
        self::assertSame('chat.example.com:6667', $connection->getServer());
    }
}
