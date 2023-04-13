#!/usr/bin/env php
<?php

// These must be at the top of your script, not inside a function
use LaswitchTech\phpConfigurator\phpConfigurator;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate phpConfigurator
$Configurator = new phpConfigurator('account');

// cURL Options
$url = $Configurator->get('account','url');
$token = $Configurator->get('account','token');

// Setup a Bearer cURL
$cURL = curl_init();
curl_setopt($cURL, CURLOPT_URL, $url . '/user/list');
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($cURL, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . base64_encode($token)]);

// Execute cURL
$response = curl_exec($cURL);

// Output Response
if (curl_errno($cURL)) {
  $response = 'Error: ' . curl_error($cURL) . PHP_EOL;
} else {
  $response = 'Response: ' . $response . PHP_EOL;
}

// Close cURL
curl_close($cURL);

//Render
?>
<!doctype html>
<html lang="en" class="h-100 w-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Response</title>
    <script src="/vendor/components/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body class="h-100 w-100">
    <div class="row h-100 w-100 m-0 p-0">
      <div class="col h-100 m-0 p-0">
        <div class="container h-100">
          <div class="d-flex h-100 row align-items-center justify-content-center">
            <div class="col">
              <h3>API <strong>Response</strong></h3>
              <pre id="Response" class="mb-4"><?= $response ?></pre>
              <div class="btn-group w-100 border shadow">
                <a href="install.php" class="btn btn-block btn-light">Re-Install</a>
                <button id="Run" type="button" class="btn btn-block btn-success">Run</button>
                <a href="/" class="btn btn-block btn-primary">Refresh</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="/dist/js/phpAPI.js"></script>
    <script>
      const API = new phpAPI()
      API.setAuth("BEARER","<?= $token ?>")
      $('#Run').click(function(){
        API.get("user/list",{
          success:function(result,status,xhr){
            $('#Response').text('Response: ' + JSON.stringify(result, null, 2))
          },
          error:function(xhr,status,error){
            $('#Response').text('Response: ' + error)
          },
        })
      })
    </script>
  </body>
</html>