<?php
// Initiate Session
session_start();

// These must be at the top of your script, not inside a function
use LaswitchTech\phpConfigurator\phpConfigurator;
use LaswitchTech\phpLogger\phpLogger;
use LaswitchTech\phpSMS\phpSMS;
use LaswitchTech\SMTP\phpSMTP;
use LaswitchTech\phpDB\Database;
use LaswitchTech\phpAUTH\phpAUTH;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate phpLogger
$phpLogger = new phpLogger();

// Configure phpLogger
$phpLogger->config("level",5); // Set Logging Level

// Initiate phpSMS
$phpSMS = new phpSMS();

// Configure phpSMS
$phpSMS->config('provider','twilio')
       ->config('sid', 'your_account_sid')
       ->config('token', 'your_auth_token')
       ->config('phone', 'your_twilio_phone_number');

// Initiate phpDB
$phpDB = new Database();

// Configure phpDB
$phpDB->config("host","localhost")
      ->config("username","demo")
      ->config("password","demo")
      ->config("database","demo3");

// Initiate phpSMTP
$phpSMTP = new phpSMTP();

// Configure phpSMTP
$phpSMTP->config("username","username@domain.com")
        ->config("password","*******************")
        ->config("host","smtp.domain.com")
        ->config("port",465)
        ->config("encryption","ssl");

// Construct Hostnames
$Hostnames = ["localhost","::1","127.0.0.1"];
$Hostname = null;
if(isset($_SERVER['SERVER_NAME']) && $Hostname === null && !in_array($_SERVER['SERVER_NAME'],$Hostnames)){
  $Hostname = $_SERVER['SERVER_NAME'];
}
if(isset($_SERVER['HTTP_HOST']) && $Hostname === null && !in_array($_SERVER['HTTP_HOST'],$Hostnames)){
  $Hostname = $_SERVER['HTTP_HOST'];
}
if($Hostname !== null){
  $Hostnames[] = $Hostname;
}

// Initiate phpAUTH
$phpAUTH = new phpAUTH();

// Configure phpAUTH
$phpAUTH->config("hostnames",$Hostnames)
        ->config("basic",false) // Enable/Disable Basic Authentication
        ->config("bearer",true) // Enable/Disable Bearer Token Authentication
        ->config("request",false) // Enable/Disable Request Authentication
        ->config("cookie",false) // Enable/Disable Cookie Authentication
        ->config("session",false) // Enable/Disable Session Authentication
        ->config("2fa",false) // Enable/Disable 2-Factor Authentication
        ->config("maxAttempts",5) // Max amount of authentication attempts per windowAttempts
        ->config("maxRequests",1000) // Max amount of API request per windowRequests
        ->config("lockoutDuration",1800) // 30 mins
        ->config("windowAttempts",100) // 100 seconds
        ->config("windowRequests",60) // 60 seconds
        ->config("window2FA",60) // 60 seconds
        ->config("windowVerification",2592000) // 30 Days
        ->init();

// Install phpAUTH
$Installer = $phpAUTH->install();

// Create a User
$User = $Installer->create("api",["username" => "username@domain.com"]);

// Activate User
$User->activate();

// Verify User
$User->verify();

// Initiate phpConfigurator
$Configurator = new phpConfigurator('account');

// Save Account for Testing
$Configurator->set('account','url',"https://{$Hostname}/api.php")
             ->set('account','token',$User->get('username').":".$User->getToken());

//Render
?>
<!doctype html>
<html lang="en" class="h-100 w-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Install</title>
    <script src="/vendor/components/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body class="h-100 w-100">
    <div class="row h-100 w-100 m-0 p-0">
      <div class="col h-100 m-0 p-0">
        <div class="container h-100">
          <div class="d-flex h-100 row align-items-center justify-content-center">
            <div class="col">
              <h3>Installation <strong>Completed</strong></h3>
              <p class="mb-4">
                <span>
                  <strong>Token:</strong> <?= $User->get('username'); ?>:<?= $User->getToken(); ?>
                </span>
              </p>
              <div class="btn-group w-100 border shadow">
                <a href="install.php" class="btn btn-block btn-light">Re-Install</a>
                <a href="/" class="btn btn-block btn-primary">Index</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
