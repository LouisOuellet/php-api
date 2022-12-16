<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

//Import Factory class into the global namespace
use Composer\Factory;

class BaseController {

  protected $Path = null;

  public function __construct(){
    $this->Path = dirname(\Composer\Factory::getComposerFile());
  }

  public function __call($name, $arguments) {
    $this->output(str_replace('Action','',$name), array('HTTP/1.1 501 Not Implemented'));
  }

  protected function getUriSegments() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );
    return $uri;
  }

  protected function getQueryStringParams() {
    parse_str($_SERVER['QUERY_STRING'], $query);
    return $query;
  }

  protected function getQueryStringBody() {
    $query = [];
    foreach($_POST as $key => $value){
      $query[$key] = base64_decode(urldecode($value));
    }
    return $query;
  }

  protected function output($data, $httpHeaders=array()) {
    header_remove('Set-Cookie');
    if (is_array($httpHeaders) && count($httpHeaders)) {
      foreach ($httpHeaders as $httpHeader) {
        header($httpHeader);
      }
    }
    echo $data;
    exit;
  }

  protected function mkdir($directory){
    $make = dirname(__FILE__,3);
    $directories = explode('/',$directory);
    foreach($directories as $subdirectory){
      $make .= '/'.$subdirectory;
      if(!is_file($make)&&!is_dir($make)){ mkdir($make); }
    }
    return $make;
  }
}
