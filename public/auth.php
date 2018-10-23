<?php
require_once('vendor/autoload.php');


// Check if this is a POST request to verify the auth code
if(post('code')) {

  // Sanitize the input first
  $code = preg_replace('/[^A-Za-z0-9]/', '', post('code'));

  // Check that the code exists
  $codeData = redis()->get('indieauth:code:'.$code);
  if(!$codeData) {
    error('invalid_request', 'The authorization code provided was not found', 400);
  }

  $data = json_decode($codeData, true);

  respond([
    'me' => Config::$base.$data['username'],
  ]);

  exit;
}

// Check if this is a POST request by the user to confirm the application
if(post('username')) {

  $csrf = preg_replace('/[^A-Za-z0-9]/', '', post('csrf'));
  if(!($data = redis()->get('indieauth:csrf:'.$csrf))) {
    error('invalid_csrf');
  }
  $data = json_decode($data, true);
  redis()->del('indieauth:csrf:'.$csrf);

  // Check that the username and password exist
  if(!Users::exists(post('username'))) {
    error('invalid_user');
  }

  if(!Users::password(post('username'), post('password'))) {
    error('invalid_password');
  }

  // Generate an authorization code and redirect to the application
  $code = random_string();
  $data['username'] = post('username');
  redis()->setex('indieauth:code:'.$code, 60, json_encode($data));

  $redirect_uri = p3k\url\add_query_params_to_url($data['redirect_uri'], [
    'code' => $code,
    'state' => $data['state'],
  ]);

  header('Location: '.$redirect_uri);

  exit;
}

// Otherwise it's a GET request to begin an authorization request

// Check for required parameters
if(!get('client_id')) {
  error('invalid_request', 'Missing client_id', 400);
}

$client_id = get('client_id');

if(!p3k\url\is_url($client_id)) {
  error('invalid_request', 'The provided client_id is not a URL', 400);
}

if(!get('redirect_uri')) {
  error('invalid_request', 'Missing redirect_uri', 400);
}

$redirect_uri = get('redirect_uri');

if(!p3k\url\is_url($redirect_uri)) {
  error('invalid_request', 'The provided redirect_uri is not a URL', 400);
}

$redirect_uri_warning = parse_url($client_id, PHP_URL_HOST) != parse_url($redirect_uri, PHP_URL_HOST);

$response_type = get('response_type', 'id');

if(!in_array($response_type, ['id','code'])) {
  error('unsupported_response_type', 'Unsupported response type', 400);
}

$scope = get('scope');

$csrf = random_string();
redis()->setex('indieauth:csrf:'.$csrf, 300, json_encode([
  'response_type' => $response_type,
  'client_id' => $client_id,
  'redirect_uri' => $redirect_uri,
  'scope' => $scope,
  'state' => get('state'),
]));

// Show the authorization prompt to the user
?>
<html>
<title>IndieAuth</title>
<style>

</style>
<body>

  <h1>IndieAuth</h1>

  <?php if($response_type == 'id'): ?>
    <p>Enter your username and password below to log in to <?= e($client_id) ?>.</p>
    <p>You will be redirected to the application at <?= e($redirect_uri) ?> when finished.</p>
  <?php else: ?>
    <p>The application <?= e($client_id) ?> is requesting the following scopes.</p>
    <p><code><?= e($scope) ?></code></p>
    <p>Enter your username and password below to grant the application access to your account.</p>
    <p>You will be redirected to the application at <?= e($redirect_uri) ?> when finished.</p>
  <?php endif ?>

  <form action="" method="post">
    <input type="text" name="username">
    <input type="password" name="password">
    <input type="submit" value="Log In">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
  </form>

</body>
</html>
