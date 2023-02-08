<?php
//Configure Cookie Scope
session_set_cookie_params(['samesite' => 'None']);

//Initiate Session
session_start();

//Import phpAPI class into the global namespace
use LaswitchTech\phpAPI\phpAPI;

// Define Root Path
define('ROOT_PATH',__DIR__);

//Load Composer's autoloader
require 'vendor/autoload.php';

new phpAPI();
