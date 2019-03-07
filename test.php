<?php

require_once './vendor/autoload.php';

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;

$options = new ClientOptions('Pokedex', ['#pokedextest']);
$options->floodProtectionDelay = 750;

$client = new IrcClient('euroserv.fr.quakenet.org:6667', $options);
$client->connect();
