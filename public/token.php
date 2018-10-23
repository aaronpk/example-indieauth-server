<?php
require_once('../vendor/autoload.php');

// Sanitize the input first
$code = preg_replace('/[^A-Za-z0-9]/', '', post('code'));

// Check that the code exists
$codeData = redis()->get('indieauth:code:'.$code);
if(!$codeData) {
  error('invalid_request', 'The authorization code provided was not found', 400);
}

$data = json_decode($codeData, true);

// Issue a new access token with the requested scopes
$access_token = random_string(64);

redis()->set('indieauth:token:'.$access_token, json_encode($data));

respond([
  'me' => Config::$base.$data['username'],
  'access_token' => $access_token,
  'scope' => $data['scope'],
]);

