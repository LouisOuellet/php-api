<?php

//Declaring namespace
namespace LaswitchTech\phpAPI;

class BaseController {

  protected $Path = null;

  public function __construct(){

    // Save Root Path
    if(!defined("ROOT_PATH")){ define("ROOT_PATH",dirname(__DIR__)); }
    $this->Path = ROOT_PATH;
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

  protected function getPostParams() {
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
    if(is_array($data)){
      $data = json_encode($data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
    echo $data;
    exit;
  }
}
