<?php

namespace Jerodev\PhpIrcClient\Helpers;

class EventHandlerCollection
{
    /** @var callable[][] */
    private $eventHandlers;

    public function __construct()
    {
        $this->eventHandlers = [];
    }

    /**
     *  Register an event handler.
     *
     *  @param callable|string $event The name of the event to listen for. Pass a callable to this parameter to catch all events.
     *  @param callable|null $function The callable that will be invoked on event
     */
    public function addHandler($event, ?callable $function): void
    {
        if (is_callable($event)) {
            $function = $event;
            $event = '*';
        }

        if (!array_key_exists($event, $this->eventHandlers)) {
            $this->eventHandlers[$event] = [];
        }

        $this->eventHandlers[$event][] = $function;
    }

    /**
     *  Invoke all handlers for a specific event.
     *
     *  @param string $event The event to invoke handlers for.
     *  @param array $arguments An array of arguments to be sent to the event handler.
     */
    public function invoke(string $event, array $arguments = []): void
    {
        $handlers = array_merge($this->eventHandlers['*'] ?? [], $this->eventHandlers[$event] ?? []);
        foreach ($handlers as $handler) {
            $handler(...$arguments);
        }
    }
}
