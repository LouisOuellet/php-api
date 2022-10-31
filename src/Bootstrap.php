<?php

use Composer\Factory;

define("ROOT_PATH", dirname(\Composer\Factory::getComposerFile()));

// include main configuration file
require_once ROOT_PATH . "/config/config.php";

// include the base controller file
require_once __DIR__ . "/BaseController.php";

// include the database model file
require_once __DIR__ . "/Database.php";

// include all model files
foreach(scandir(ROOT_PATH . "/Model/") as $model){
  if(str_contains($model, 'Model.php')){
    require_once ROOT_PATH . "/Model/" . $model;
  }
}
