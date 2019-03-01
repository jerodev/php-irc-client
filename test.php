<?php

require_once './vendor/autoload.php';

use Jerodev\PhpIrcClient\IrcClient;

$client = new IrcClient('euroserv.fr.quakenet.org:6667', 'Pokedex', ['#pokedextest']);
$client->connect();
