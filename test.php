<?php

require_once './vendor/autoload.php';

use Jerodev\PhpIrcClient\IrcClient;

$client = new IrcClient('irc.quakenet.org:6667', 'Pokedex', '#pokedextest');
$client->connect();
