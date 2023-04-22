<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Jerodev\PhpIrcClient\Helpers\Event;
use Jerodev\PhpIrcClient\Helpers\EventHandlerCollection as Collection;
use Tests\TestCase;

final class EventHandlerCollectionTest extends TestCase
{
    public function testInvokeWithUniversalHandler(): void
    {
        $called = false;
        $handler = function (?array $arguments = []) use (&$called): void {
            $called = true;
        };
        $collection = new Collection();
        $collection->addHandler($handler, null);
        $collection->invoke(new Event('kick'));
        self::assertTrue($called);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testInvokeWithUnrelatedHandler(): void
    {
        $handler = function (?array $arguments = []): void {
            self::fail('Handler should not have been called');
        };
        $collection = new Collection();
        $collection->addHandler('mode', $handler);
        $collection->invoke(new Event('kick'));
    }

    public function testInvokeWithUniversalAndSpecificHandlers(): void
    {
        $collection = new Collection();

        $universalCalled = false;
        $universalHandler = function () use (&$universalCalled): void {
            $universalCalled = true;
        };
        $collection->addHandler($universalHandler, null);

        $specificCalled = false;
        $specificHandler = function () use (&$specificCalled): void {
            $specificCalled = true;
        };
        $collection->addHandler('kick', $specificHandler);

        $collection->invoke(new Event('kick'));

        self::assertTrue($universalCalled);
        self::assertTrue($specificCalled);
    }

    public function testInvokeMultipleHandlers(): void
    {
        $called = 0;
        $handler = function () use (&$called): void {
            $called++;
        };
        $collection = new Collection();
        $collection->addHandler('kick', $handler);
        $collection->addHandler('kick', $handler);
        $collection->invoke(new Event('kick'));
        self::assertSame(2, $called);
    }

    public function testInvokeReceivesEventArguments(): void
    {
        $handler = function (?array $arguments = []): void {
            self::assertSame(['foo' => 'bar'], $arguments);
        };
        $collection = new Collection();
        $collection->addHandler('kick', $handler);
        $collection->invoke(new Event('kick', [['foo' => 'bar']]));
    }
}
