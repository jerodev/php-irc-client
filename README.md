# PHP IRC Client
[![Build Status](https://travis-ci.com/jerodev/php-irc-client.svg?branch=master)](https://travis-ci.com/jerodev/php-irc-client) [![StyleCI](https://github.styleci.io/repos/173153410/shield?branch=master)](https://github.styleci.io/repos/173153410) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jerodev/php-irc-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jerodev/php-irc-client/?branch=master)

A pure PHP IRC client based on [ReactPHP](https://reactphp.org/).

## Documentation

  - [Client](#client)
    - [Connecting to the server](#client-connect)
    - [Sending commands to the server](#client-send-command)
    - [Joining a channel](#client-join-channel)
    - [Leaving a channel](#client-leave-channel)
    - [Sending messages](#client-sending-messages)
  - [Events](#events)
    - [Registered on server](#client-event-registered)
    - [Message of the day](#client-event-motd)
    - [Topic changed](#client-event-topic)
    - [Channel users received](#client-event-names)
    - [Message received](#client-event-message)
    - [Ping received](#client-event-ping)

---

## Client

The client is the heart of the library, this object is used to perform all communication between your application and the IRC server. It will manage the connection to the IRC server and has all functions needed to interact with the server.

    use Jerodev\PhpIrcClient\IrcClient;

    $client = new IrcClient('irc.server:6667', 'Jerodev');
    $client->connect();

### <a name="client-connect"></a> Connecting to the server

    $client->connect()

This function opens the connection to the IRC server. Username has to be set before the connection can be opened.

### <a name="client-send-command"></a> Sending commands to the server

    $client->send(string $command)

Sends a raw IRC command directly to the server. This method should only be used if you really know what you are doing, it is recommended to use the built-in functions below.

| Name | Type | Description
| --- | --- | --- |
| `$command` | *string* | The raw irc command


### <a name="client-join-channel"></a> Joining a channel

    $client->join(string $channel)

Joins a specified channel

| Name | Type | Description
| --- | --- | --- |
| `$channel` | *string* | The name of the channel to join.

### <a name="client-leave-channel"></a> Leaving a channel

    $client->part(string $channel)

Leave a channel. If the specified channel has not yet been joined, nothing will happen.

| Name | Type | Description
| --- | --- | --- |
| `$channel` | *string* | The name of the channel to part.

### <a name="client-sending-messages"></a> Sending messages

    $client->say(string $target, string $message)

Sends a message to a channel or user.

| Name | Type | Description
| --- | --- | --- |
| `$target` | *string* | A name of a channel staring with `#` or the nickname of the user to send a message to.
| `$message` | *string* | The message to send to the target.

---

## Events

The `on()` function on the client can be used to register to several different events. This can be done both before and after connecting to the IRC server. Events have variable callback arguments, all are described below.

### <a name="client-event-registered"></a> Registered on server

    $client->on('registered', function () { });

Emitted when the server sends the initial welcome message (`001`). This indicates that you are connected to the server.

### <a name="client-event-motd"></a> Message of the day

    $client->on('motd', function (string $motd) { });

Emitted when the server sends the message of the day to the client. If the message of the day is multiple lines, this event might be emitted multiple times.

| Name | Type | Description
| --- | --- | --- |
| `$motd` | *string* | The server's *Message Of The Day*.

### <a name="client-event-topic"></a> Topic changed

    $client->on('topic', function (string $channel, string $topic) { });

Emitted when joining a channel or when the topic of a joined channel changes.

| Name | Type | Description
| --- | --- | --- |
| `$channel` | *string* | The channel where the topic has changed.
| `$topic` | *string* | The new topic for this channel.

### <a name="client-event-names"></a> Channel users received

    $client->on('names', function (string $channel, string[] $nicks) { });

Emitted when the server sends a list of nicks for a channel. This happens immediately after joining a channel and on request.

| Name | Type | Description
| --- | --- | --- |
| `$channel` | *string* | The channel name.
| `$names` | *string[]* | A list of nicknames who are currently in this channel.

> You can also specify the channel you want to listen on by adding `#channel` to the event.<br />
> For example: `$client->on('names#channel', function ($names) {})`

### <a name="client-event-message"></a> Message received

    $client->on('message', function (string $from, IrcChannel $channel, string $message) { });

Emitted when a message is sent to a connected channel.

| Name | Type | Description
| --- | --- | --- |
| `$from` | *string* | The nickname of the user who sent the message.
| `$channel` | *IrcChannel* | The channel where the message was sent.
| `$message` | *string* | The received message.

> You can also specify the channel you want to listen on by adding `#channel` to the event.<br />
> For example: `$client->on('message#channel', function ($from, $channel, $message) {})`

### <a name="client-event-ping"></a> Ping received

    $client->on('ping', function () { });

Emitted when the server sends a `ping` request to the client. The pong request has already been sent back to the server before this event is emitted.
