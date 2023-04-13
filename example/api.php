<?php
// Initiate Session
session_start();

// These must be at the top of your script, not inside a function
use LaswitchTech\phpAPI\phpAPI;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate phpAPI
new phpAPI();
