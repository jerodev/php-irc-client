<?php

declare(strict_types=1);

namespace Tests;

use Error;
use Exception;
use Jerodev\PhpIrcClient\IrcChannel;

final class IrcChannelTest extends TestCase
{
    public function testEmptyChannelName(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Channel name is empty.');
        new IrcChannel(' ');
    }

    public function testSetChannelNameWithSpace(): void
    {
        $channel = new IrcChannel(' testing ');
        self::assertSame('#testing', $channel->getName());
    }

    public function testGetTopicNotSet(): void
    {
        $channel = new IrcChannel('#irc-help');
        self::expectException(Error::class);
        $channel->getTopic();
    }

    public function testGetTopic(): void
    {
        $channel = new IrcChannel('topical-humor');
        $channel->setTopic('Humor about topics');
        self::assertSame('Humor about topics', $channel->getTopic());
    }

    public function testGetUsers(): void
    {
        $channel = new IrcChannel('irc-help');
        self::assertCount(0, $channel->getUsers());

        $channel->setUsers(['+bob', '@dark-roach']);
        self::assertCount(2, $channel->getUsers());
        self::assertSame('bob', $channel->getUsers()[0]);
        self::assertSame('dark-roach', $channel->getUsers()[1]);
    }
}
