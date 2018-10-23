<?php
require_once('vendor/autoload.php');
?>
<html>
<head>
  <title><?= Config::$name ?></title>
  <link rel="authorization_endpoint" href="auth.php">
  <link rel="token_endpoint" href="token.php">
  <link rel="micropub" href="micropub.php">
</head>
<body>

<h1><?= Config::$name ?></h1>

<p>If you have an account here, you can sign in to IndieAuth apps!</p>

<p>Enter <code><?= Config::$base ?></code> in the web sign-in prompt of your application.</p>

</code>
</html>
