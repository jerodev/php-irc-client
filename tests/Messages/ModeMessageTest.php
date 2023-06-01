<?php

declare(strict_types=1);

use Jerodev\PhpIrcClient\Messages\ModeMessage;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
final class ModeMessageTest extends TestCase
{
    protected const SERVER_MODE = ':Commlink!~Commlink@freenode-lj6.pif.i47qc9.IP MODE Commlink :+wRix';
    protected const VOICE_MODE = ':omni!~omni@freenode/user/omni MODE #commlink +v :Commlink';

    public function testConstructorWithNoChannel(): void
    {
        $mode = new ModeMessage(self::SERVER_MODE);
        self::assertSame('+wRix', $mode->mode);
        self::assertNull($mode->channel);
        self::assertSame('Commlink', $mode->user);
        self::assertNull($mode->target);

        $events = $mode->getEvents();
        self::assertCount(1, $events);
        $event = $events[0];
        self::assertSame('mode', $event->getEvent());
    }

    public function testConstructorWithChannel(): void
    {
        $mode = new ModeMessage(self::VOICE_MODE);
        self::assertSame('+v', $mode->mode);
        self::assertSame('#commlink', $mode->channel->getName());
        self::assertSame('Commlink', $mode->user);
        self::assertSame('#commlink', $mode->target);
    }
}
