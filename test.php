<?php

require_once './vendor/autoload.php';

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;

$options = new ClientOptions('PokedexTest', ['#pokedextest']);
$options->autoConnect = true;
$options->floodProtectionDelay = 750;

$client = new IrcClient('euroserv.fr.quakenet.org:6667', $options);
