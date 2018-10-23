<?php
date_default_timezone_set('UTC');
require('config.php');

function redis() {
  static $client = false;
  if(!$client)
    $client = new Predis\Client(Config::$redis);
  return $client;
}

function e($text) {
  return htmlspecialchars($text);
}

function get($k, $d=null) {
  return $_GET[$k] ?? $d;
}

function post($k, $d=null) {
  return $_POST[$k] ?? $d;
}

function random_string($len=32) {
  $charset='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
  $str = '';
  $c = strlen($charset)-1;
  for($i=0; $i<$len; $i++) {
    $str .= $charset[mt_rand(0, $c)];
  }
  return $str;
}

function error($error, $description=null, $code=400) {
  $data = [
    'error' => $error
  ];
  if($description) {
    $data['error_description'] = $description;
  }
  respond($data, $code);
}

function respond($data, $code=200) {
  header('HTTP/1.1 '.$code);
  header('Content-type: application/json');
  header('Access-Control-Allow-Origin: *');
  echo json_encode($data, JSON_UNESCAPED_SLASHES);
  exit;
}
