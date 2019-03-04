# PHP IRC Client
[![Build Status](https://travis-ci.com/jerodev/php-irc-client.svg?branch=master)](https://travis-ci.com/jerodev/php-irc-client) [![StyleCI](https://github.styleci.io/repos/173153410/shield?branch=master)](https://github.styleci.io/repos/173153410) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jerodev/php-irc-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jerodev/php-irc-client/?branch=master)

A pure PHP irc client based on [ReactPHP](https://reactphp.org/).

> :wrench: This project is under development and will probably not work in its current state.

## Documentation

  - [Client](#client)
    - [Connecting to the server](#client-connect)
    - [Sending commands to the server](#client-send-command)
    - [Joining a channel](#client-join-channel)
    - [Leaving a channel](#client-leave-channel)
    - [Sending messages](#client-sending-messages)
  - [Events](#events)
    - [Message received](#client-event-message)

---

## Client

The client is the heart of the library, this object is used to perform all communication between your application and the irc server. It will manage the connection to the irc server and has all functions needed to interact with the server.

    use Jerodev\PhpIrcClient\IrcClient;

    $client = new IrcClient('irc.server:6667', 'Jerodev');
    $client->connect();

### <a name="client-connect"></a> Connecting to the server

    $client->connect()

This function opens the connection to the irc server. A username has to be set before the connection can be opened.

### <a name="client-send-command"></a> Sending commands to the server

    $client->send(string $command)

Sends a raw irc command directly to the server. This method should only be used if you are realy know what you are doing, it is recommended to use the built-in functions below.

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

The `on()` function on the client can be used to register to several different events. This can be done both before and after connecting to the irc server. Events might have different callback arguments, all are described below.

### <a name="client-event-message"></a> Message received

    $client->on('message', function (string $from, string $to, string $message) {
        //
    });

Emitted when a message is sent to the user or to a joined channel.

| Name | Type | Description
| --- | --- | --- |
| `$from` | *string* | The nickname of the user who sent the message.
| `$to` | *string* | The channel where the message was sent, or the name of the client in case of a private message.
| `$message` | *string* | The received message.

> You can also specify the channel you want to listen on by adding `#channel` to the event.<br />
> For example: `$client->on('message#channel', function ($from, $message) {})`

