<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

//Import Factory, Auth, phpSMTP and Database classes into the global namespace
use Composer\Factory;
use LaswitchTech\phpDB\Database;
use LaswitchTech\phpAUTH\Auth;
use LaswitchTech\SMTP\phpSMTP;

class phpAPI {

  protected $URI;
  protected $Path;
  protected $Settings;
  protected $Manifest;
  protected $Debug = false;

  public function __construct() {

    // Set Cookie Scope
    ini_set('session.cookie_samesite', 'None');

    // Configure API
    $this->configure();

    // Include all model files
    if(is_dir($this->Path . "/Model")){
      foreach(scandir($this->Path . "/Model/") as $model){
        if(str_contains($model, 'Model.php')){
          require_once $this->Path . "/Model/" . $model;
        }
      }
    }

    // Parse URL
    $this->URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->URI = explode( '/', $this->URI );
    if(isset($this->URI[2],$this->URI[3])){

      // Identify Controller
      $strControllerName = ucfirst($this->URI[2]) . "Controller";
      $strMethodName = $this->URI[3] . 'Action';
      if(is_file($this->Path . "/Controller/" . $strControllerName . ".php")){

        // Load Controller
        require $this->Path . "/Controller/" . $strControllerName . ".php";

        // Create Controller
        $objFeedController = new $strControllerName();
        $objFeedController->{$strMethodName}();
      } else {

        // Could not find Controller
        $this->sendOutput('Could not find Controller', array('HTTP/1.1 404 Not Found'));
      }
    } else {

      // Could not identify the Controller and/or Method
      $this->sendOutput('Could not identify the Controller and/or Action', array('HTTP/1.1 422 Unprocessable Entity'));
    }
  }

  public function __call($name, $arguments) {
    $this->sendOutput($name, array('HTTP/1.1 501 Not Implemented'));
  }

  protected function sendOutput($data, $httpHeaders=array()) {
    header_remove('Set-Cookie');
    if (is_array($httpHeaders) && count($httpHeaders)) {
      foreach ($httpHeaders as $httpHeader) {
        header($httpHeader);
      }
    }
    echo $data;
    exit;
  }

  public function getPath(){
    return $this->Path;
  }

  protected function configure(){

    // Save Root Path
    $this->Path = dirname(\Composer\Factory::getComposerFile());
    if(!defined("ROOT_PATH")){ define("ROOT_PATH", $this->Path); }

    // Include manifest configuration file
    if(is_file($this->Path . "/src/manifest.json")){

      // Save all settings
      $this->Manifest = json_decode(file_get_contents($this->Path . '/src/manifest.json'),true);

      // MySQL Debug
      if(isset($this->Manifest['sql']['debug'])){
        $this->Debug = $this->Manifest['sql']['debug'];
      }

      // Auth Configuration Information
      if(isset($this->Manifest['auth']['roles'])){
        if(!defined("AUTH_ROLES")){ define("AUTH_ROLES", $this->Manifest['auth']['roles']); }
      } else {
        if(!defined("AUTH_ROLES")){ define("AUTH_ROLES", true); }
      }
      if(isset($this->Manifest['auth']['groups'])){
        if(!defined("AUTH_GROUPS")){ define("AUTH_GROUPS", $this->Manifest['auth']['groups']); }
      } else {
        if(!defined("AUTH_GROUPS")){ define("AUTH_GROUPS", false); }
      }
      if(isset($this->Manifest['auth']['type']['api'])){
        if(!defined("AUTH_F_TYPE")){ define("AUTH_F_TYPE", $this->Manifest['auth']['type']['api']); }
      } else {
        if(!defined("AUTH_F_TYPE")){ define("AUTH_F_TYPE", "BEARER"); }
      }
    } else {

      // Auth Configuration Information
      if(!defined("AUTH_ROLES")){ define("AUTH_ROLES", true); }
      if(!defined("AUTH_GROUPS")){ define("AUTH_GROUPS", false); }
      if(!defined("AUTH_F_TYPE")){ define("AUTH_F_TYPE", "BEARER"); }
    }

    // Include main configuration file
    if(is_file($this->Path . "/config/config.json")){

      // Save all settings
    	$this->Settings = json_decode(file_get_contents($this->Path . '/config/config.json'),true);

      //MySQL Configuration Information
      if(isset($this->Settings['sql'])){
        if(!defined("DB_HOST")){ define("DB_HOST", $this->Settings['sql']['host']); }
        if(!defined("DB_USERNAME")){ define("DB_USERNAME", $this->Settings['sql']['username']); }
        if(!defined("DB_PASSWORD")){ define("DB_PASSWORD", $this->Settings['sql']['password']); }
        if(!defined("DB_DATABASE_NAME")){ define("DB_DATABASE_NAME", $this->Settings['sql']['database']); }

        // MySQL Debug
        if(isset($this->Settings['sql']['debug'])){
          $this->Debug = $this->Settings['sql']['debug'];
        }
      }
    } else {

      // Could not find settings
      $this->sendOutput('Could not find settings', array('HTTP/1.1 422 Unprocessable Entity'));
    }

    // MySQL Debug
    if(!defined("DB_DEBUG")){ define("DB_DEBUG", $this->Debug); }
  }
}
