<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Jerodev\PhpIrcClient\Helpers\Event;
use Tests\TestCase;

final class EventTest extends TestCase
{
    public function testGetArgumentsEmpty(): void
    {
        $event = new Event('testing');
        self::assertSame([], $event->getArguments());
    }

    public function testGetArguments(): void
    {
        $arguments = [
            'foo' => 'bar',
        ];
        $event = new Event('testing', $arguments);
        self::assertSame($arguments, $event->getArguments());
    }

    public function testGetEvent(): void
    {
        $event = new Event('event name');
        self::assertSame('event name', $event->getEvent());
    }
}
