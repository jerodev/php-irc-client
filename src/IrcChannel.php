<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient;

use Exception;

class IrcChannel
{
    private string $name;
    private ?string $topic;

    /** @var array<int, string> */
    private array $users = [];

    public function __construct(string $name)
    {
        $name = trim($name);
        if ('' === $name || '#' === $name) {
            throw new Exception('Channel name is empty.');
        }

        $this->name = $name;
        if ($this->name[0] !== '#') {
            $this->name = '#' . $this->name;
        }
    }

    /**
     * Fetch the name of the channel, including the `#`.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the current channel topic.
     */
    public function getTopic(): ?string
    {
        return $this->topic;
    }

    /**
     * Fetch the list of users currently on this channel.
     * @return array<int, string>
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * Set the current channel topic.
     * @param string $topic The topic
     */
    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    /**
     * Set the list of active users on the channel.
     * User modes (`+`, `@`) will be removed from the nicknames.
     * @param array<int, string> $users An array of user names.
     */
    public function setUsers(array $users): void
    {
        $this->users = array_map(function ($user): string {
            if (in_array($user[0], ['+', '@'])) {
                $user = substr($user, 1);
            }

            return $user;
        }, $users);
    }
}
