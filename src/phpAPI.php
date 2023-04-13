<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

// Import phpConfigurator class into the global namespace
use LaswitchTech\phpConfigurator\phpConfigurator;

// Import phpLogger class into the global namespace
use LaswitchTech\phpLogger\phpLogger;

// Import Auth Class into the global namespace
use LaswitchTech\phpAUTH\phpAUTH;

class phpAPI {

	// phpLogger
	private $Logger;

  // phpConfigurator
  private $Configurator = null;

	// phpAUTH
  private $Auth = null;

  // URI
  private $URI;

  public function __construct() {

    // Initialize Configurator
    $this->Configurator = new phpConfigurator('api');

    // Initiate phpLogger
    $this->Logger = new phpLogger('api');

    // Initiate phpAuth
    $this->Auth = new phpAuth();

    // Include all model files
    if(is_dir($this->Configurator->root() . "/Model")){
      foreach(scandir($this->Configurator->root() . "/Model/") as $model){
        if(str_contains($model, 'Model.php')){
          require_once $this->Configurator->root() . "/Model/" . $model;
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
      if(is_file($this->Configurator->root() . "/Controller/" . $strControllerName . ".php")){

        // Load Controller
        require $this->Configurator->root() . "/Controller/" . $strControllerName . ".php";

        // Create Controller
        $objFeedController = new $strControllerName($this->Auth);

        // Call Method
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

  private function sendOutput($data, $httpHeaders=array()) {

    // Remove the default Set-Cookie header
    header_remove('Set-Cookie');

    // Add the custom headers
    if (is_array($httpHeaders) && count($httpHeaders)) {
      foreach ($httpHeaders as $httpHeader) {
        header($httpHeader);
      }
    }

    // Check if the data is an array or object
    if(is_array($data) || is_object($data)){

      // Convert the data to JSON
      $data = json_encode($data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    // Send the output
    echo $data;

    // Exit the script
    exit;
  }
}
