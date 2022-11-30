<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

//Import Factory, Auth and Database classes into the global namespace
use Composer\Factory;
use LaswitchTech\phpDB\Database;
use LaswitchTech\phpAUTH\Auth;

class phpAPI {

  protected $URI;
  protected $Path;
  protected $Settings;
  protected $Manifest;
  protected $Debug = false;

  public function __construct() {

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
        header("HTTP/1.1 404 Not Found");
        exit();
      }
    } else {

      // Could not identify the Controller and/or Method
      header("HTTP/1.1 404 Not Found");
      exit();
    }
  }

  public function getPath(){
    return $this->Path;
  }

  protected function configure(){

    // Save Root Path
    $this->Path = dirname(\Composer\Factory::getComposerFile());
    define("ROOT_PATH", $this->Path);

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
        define("AUTH_ROLES", $this->Manifest['auth']['roles']);
      } else {
        define("AUTH_ROLES", true);
      }
      if(isset($this->Manifest['auth']['groups'])){
        define("AUTH_GROUPS", $this->Manifest['auth']['groups']);
      } else {
        define("AUTH_GROUPS", false);
      }
      if(isset($this->Manifest['auth']['type']['api'])){
        define("AUTH_F_TYPE", $this->Manifest['auth']['type']['api']);
      } else {
        define("AUTH_F_TYPE", "BEARER");
      }
    } else {

      // Auth Configuration Information
      define("AUTH_ROLES", true);
      define("AUTH_GROUPS", false);
      define("AUTH_F_TYPE", "BEARER");
    }

    // Include main configuration file
    if(is_file($this->Path . "/config/config.json")){

      // Save all settings
    	$this->Settings = json_decode(file_get_contents($this->Path . '/config/config.json'),true);

      //MySQL Configuration Information
      define("DB_HOST", $this->Settings['sql']['host']);
      define("DB_USERNAME", $this->Settings['sql']['username']);
      define("DB_PASSWORD", $this->Settings['sql']['password']);
      define("DB_DATABASE_NAME", $this->Settings['sql']['database']);

      // MySQL Debug
      if(isset($this->Settings['sql']['debug'])){
        $this->Debug = $this->Settings['sql']['debug'];
      }
    } else {

      // Could not find settings
      header_remove('Set-Cookie');
      header('HTTP/1.1 500 Internal Server Error');
      exit;
    }

    // MySQL Debug
    define("DB_DEBUG", $this->Debug);
  }
}
