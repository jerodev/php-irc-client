<?php

namespace Jerodev\PhpIrcClient;

class IrcChannel
{
    /** @var string */
    private $name;

    /** @var string[] */
    private $users;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->users = [];
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
     *  Set the list of active users on the channel.
     *
     *  @param string[] $users An array of user names.
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}
