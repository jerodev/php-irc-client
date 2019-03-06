<?php

namespace Jerodev\PhpIrcClient\Helpers;

class Event
{
    /** @var string */
    private $event;

    /** @var array */
    private $arguments;

    /**
     *  @param string $event The event that is being emitted.
     *  @param array $arguments The array of arguments to send to the event callback.
     */
    public function __construct(string $event, $arguments = [])
    {
        $this->event = $event;
        $this->arguments = $arguments;
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
