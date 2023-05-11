<?php

declare(strict_types=1);

namespace Jerodev\PhpIrcClient;

final class IrcCommand
{
    public const RPL_WELCOME = '001';
    public const RPL_TOPIC = '332';
    public const RPL_NAMREPLY = '353';
    public const RPL_MOTD = '372';
}
