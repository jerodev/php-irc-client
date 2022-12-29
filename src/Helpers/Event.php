<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient\Helpers;

class Event
{
    /**
     *  @param string $event The event that is being emitted.
     *  @param array $arguments The array of arguments to send to the event callback.
     */
    public function __construct(private string $event, private array $arguments = [])
    {
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getEvent(): string
    {
        return $this->event;
    }
}
