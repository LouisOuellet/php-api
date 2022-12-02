<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

//Import Database class into the global namespace
use LaswitchTech\phpDB\Database;

class BaseModel extends Database {

  public function __call($name, $arguments) {
    return [ "error" => "[".$name."] 501 Not Implemented" ];
  }
}
