<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

// Import phpConfigurator class into the global namespace
use LaswitchTech\phpConfigurator\phpConfigurator;

// Import phpLogger class into the global namespace
use LaswitchTech\phpLogger\phpLogger;

//Import Database class into the global namespace
use LaswitchTech\phpDB\Database;

class BaseModel extends Database {

	// phpLogger
	protected $Logger;

  // phpConfigurator
  protected $Configurator = null;

  public function __construct(){

    // Initiate Database
    parent::__construct();
  }

  public function __call($name, $arguments) {

    // Log the error
    $this->Logger->error("[".$name."] 501 Not Implemented");

    // Return the error
    return "[".$name."] 501 Not Implemented";
  }
}
