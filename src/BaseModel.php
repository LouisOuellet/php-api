<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

//Import Database class into the global namespace
use LaswitchTech\phpDB\Database;

class BaseModel extends Database {

  public function __call($name, $arguments) {

    // Log the error
    $this->Logger->error("[".$name."] 501 Not Implemented");

    // Return the error
    return "[".$name."] 501 Not Implemented";
  }
}
