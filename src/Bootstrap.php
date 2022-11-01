<?php

//Import Factory, Auth and Database classes into the global namespace
use Composer\Factory;
use LaswitchTech\phpDB\Database;
use LaswitchTech\phpAUTH\Auth;

//Defining ROOT_PATH
define("ROOT_PATH", dirname(\Composer\Factory::getComposerFile()));

// Include main configuration file
if(is_file(ROOT_PATH . "/config/config.php")){
  require_once ROOT_PATH . "/config/config.php";
} else {
  // Insert Installer Here
  header_remove('Set-Cookie');
  header('HTTP/1.1 500 Internal Server Error');
  exit;
}

// Include the Base Controller file
require_once __DIR__ . "/BaseController.php";

// Include all model files
foreach(scandir(ROOT_PATH . "/Model/") as $model){
  if(str_contains($model, 'Model.php')){
    require_once ROOT_PATH . "/Model/" . $model;
  }
}
