<?php

//Declaring namespace
namespace LaswitchTech\API;

class API {

  protected $URI;

  public function __construct() {
    require __DIR__ . "/bootstrap.php";
    $this->URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->URI = explode( '/', $this->URI );
    if(isset($this->URI[2],$this->URI[3])){
      $strControllerName = ucfirst($this->URI[2]) . "Controller";
      $strMethodName = $this->URI[3] . 'Action';
      if(is_file(ROOT_PATH . "/Controller/" . $strControllerName . ".php")){
        require ROOT_PATH . "/Controller/" . $strControllerName . ".php";
        $objFeedController = new $strControllerName();
        $objFeedController->{$strMethodName}();
      } else {
        header("HTTP/1.1 404 Not Found");
        exit();
      }
    } else {
        header("HTTP/1.1 404 Not Found");
        exit();
    }
  }
}
