<?php

declare(strict_types=1);

namespace Tests;

use Exception;
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

    public function testAutoRejoin(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        self::assertFalse($client->shouldAutoRejoin());

        $options = new ClientOptions('php-irc-bot');
        $options->autoRejoin = true;
        $client = new IrcClient('chat.example.com:6667', $options);
        self::assertTrue($client->shouldAutoRejoin());
    }

    public function testGetNicknameWithoutUser(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        self::assertNull($client->getNickname());
    }

    public function testGetNicknameFromOptions(): void
    {
        $options = new ClientOptions('PhpIrcBot');
        $client = new IrcClient('chat.example.com:6667', $options);
        self::assertSame('PhpIrcBot', $client->getNickname());
    }

    public function testGetNicknameFromSetUser(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        $client->setUser('test-bot');
        self::assertSame('test-bot', $client->getNickname());
    }

    public function testGetChannelsNone(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        self::assertCount(0, $client->getChannels());
    }

    public function testGetChannelsFromOptions(): void
    {
        $options = new ClientOptions('PhpIrcBot', ['#php-irc-client-test']);
        $client = new IrcClient('chat.example.com:6667', $options);
        self::assertCount(1, $client->getChannels());
    }

    public function testGetChannelNewChannel(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        self::assertCount(0, $client->getChannels());
        $client->getChannel('testing');
        self::assertCount(1, $client->getChannels());

        $channels = $client->getChannels();
        self::assertSame('#testing', $channels['#testing']->getName());
    }

    public function testConnectWithoutUser(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        self::expectException(Exception::class);
        self::expectExceptionMessage(
            'A nickname must be set before connecting to an irc server.'
        );
        $client->connect();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDisconnectWithoutConnecting(): void
    {
        $client = new IrcClient('chat.example.com:6667');
        $client->disconnect();
    }
}
