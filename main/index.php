<?php

// Define Constant Variables
define("DS", DIRECTORY_SEPARATOR);
define("ROOT", dirname(dirname(__FILE__)));
define("VENDORS", ROOT.DS.'vendor');
define("VIEW_PATH", ROOT.DS.'main/layout');
define("CACHE_PATH", ROOT.DS.'cached/layout');

// Load the Local Class Files Automatically when called
require_once ROOT.DS.'main/autoloader.php';

// This Runs the whole application
App::run($_SERVER);

// Load the Configuration File
require_once ROOT.DS. 'main/conf.php';