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

$signal_handler = function (int $signo, mixed $siginfo) use ($client, $server): void {
    if (SIGHUP === $signo) {
        echo 'Caught signal to re-read config', PHP_EOL;
        return;
    }

    if (SIGTSTP === $signo) {
        echo 'Caught sleep signal', PHP_EOL;

        // Restore original handler.
        pcntl_signal(SIGTSTP, SIG_DFL);
        posix_kill(posix_getpid(), SIGTSTP);
        return;
    }

    if (SIGCONT === $signo) {
        echo 'Caught continue signal', PHP_EOL;
        return;
    }

    if (SIGINT !== $signo && SIGTERM !== $signo) {
        echo 'Caught unknown signal (', $signo, ')', PHP_EOL;
        return;
    }

    // Handle shutdown tasks.
    echo 'Disconnecting from ', $server, PHP_EOL;
    foreach ($client->getChannels() as $name => $channel) {
        $client->part($name);
    }
    $client->disconnect();
    exit();
};

pcntl_signal(SIGHUP, $signal_handler); // kill -HUP <pid>
pcntl_signal(SIGINT, $signal_handler); // CTRL-C
pcntl_signal(SIGTERM, $signal_handler); // kill <pid>
pcntl_signal(SIGTSTP, $signal_handler); // CTRL-Z
pcntl_signal(SIGCONT, $signal_handler); // fg after a CTRL-Z

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
