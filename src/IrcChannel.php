<?php

namespace Jerodev\PhpIrcClient;

class IrcChannel
{
    /** @var string */
    private $name;

    /** @var null|string */
    private $topic;

    /** @var string[] */
    private $users;

    public function __construct(string $name)
    {
        $name = trim($name);
        if (empty($name)) {
            throw new Exception('Channel name is empty.');
        }

        if ($name[0] !== '#') {
            $name = "#$name";
        }
        $this->name = $name;
        $this->users = [];
    }

    /**
     *  Fetch the name of the channel, including the `#`.
     *
     *  @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *  Get the current channel topic.
     *
     *  @return null|string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     *  Fetch the list of users currently on this channel.
     *
     *  @return string[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     *  Set the current channel topic.
     *
     *  @param string $topic The topic
     */
    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    /**
     *  Set the list of active users on the channel.
     *  User modes (`+`, `@`) will be removed from the nicknames.
     *
     *  @param string[] $users An array of user names.
     */
    public function setUsers(array $users): void
    {
        $this->users = array_map(function ($user) {
            if (in_array($user[0], ['+', '@'])) {
                $user = substr($user, 1);
            }

            return $user;
        }, $users);
    }
}
