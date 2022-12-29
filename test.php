<?php

declare(strict_types=1);

require_once './vendor/autoload.php';

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\Options\ClientOptions;

// Set the IRC network to connect to and the port if you're not connecting to
// Freenode.
$server = 'chat.freenode.net';
$port = '6667';

// Give your bot a memorable name.
$nickname = 'PHP_IRC_Bot';

// If you add any channels (like ['#php-is-neat']), the bot will automatically
// join them when you run `php test.php`.
$autojoinChannels = [];

$options = new ClientOptions(nickname: $nickname, channels: $autojoinChannels);
$client = new IrcClient(\sprintf('%s:%s', $server, $port), $options);

$client->on('registered', function () use ($server, $port) {
    echo \sprintf('Connected to %s, port %s', $server, $port), PHP_EOL;
});

$client->on(
    'message',
    function (
        string $from,
        IrcChannel $channel,
        string $message
    ) use ($client, $nickname): void {
        echo \sprintf(
            ' . %10s - %10s: %s',
            $channel->getName(),
            $from,
            $message
        ), PHP_EOL;

        if ($nickname === $from) {
            // Ignore messages from the bot.
            return;
        }

        if (false === str_contains($message, $nickname)) {
            // Ignore messages that aren't to the bot.
            return;
        }

        echo \sprintf(
            ' . %10s - %10s: %s',
            $channel->getName(),
            $nickname,
            'I am not a bot!',
        ), PHP_EOL;
        $client->say($channel->getName(), 'I am not a bot!');
    }
);

$client->connect();
