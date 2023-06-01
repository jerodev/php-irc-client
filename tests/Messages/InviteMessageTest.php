<?php

declare(strict_types=1);

namespace Tests\Messages;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\Messages\InviteMessage;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
final class InviteMessageTest extends TestCase
{
    protected const MESSAGE = ':username!~remoteUsername@network/user/fullUsername INVITE Invited :#channel';

    public function testConstructor(): void
    {
        $invite = new InviteMessage(self::MESSAGE);
        self::assertSame('#channel', $invite->target);
        self::assertSame('#channel', $invite->channel->getName());
        self::assertSame('username', $invite->user);
    }

    public function testEvents(): void
    {
        $events = (new InviteMessage(self::MESSAGE))->getEvents();

        self::assertCount(1, $events);
        $event = $events[0];
        self::assertSame('invite', $event->getEvent());
        $arguments = $event->getArguments();
        self::assertSame('#channel', $arguments[0]->getName());
        self::assertSame('username', $arguments[1]);
    }
}
