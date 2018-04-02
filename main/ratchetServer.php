#!/usr/bin/env php
<?php

// Define Constant Variables
define("DS", DIRECTORY_SEPARATOR);
define("ROOT", dirname(dirname(__FILE__)));
define("VENDORS", ROOT.DS.'vendor');

// Load the Vendor Files Automatically when called
require_once VENDORS.DS.'autoload.php';

// Include the required file
require_once ROOT.DS.'lib/processors/RatchetChat.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$ws = new WsServer( new RatchetChat );

$server = IoServer::factory(
	new HttpServer($ws), 2000
);

$server->run();
