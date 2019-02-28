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
        $this->name = $name;
        $this->users = [];
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
     *
     *  @param string[] $users An array of user names.
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}
