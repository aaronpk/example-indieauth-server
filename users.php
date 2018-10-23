<?php

class Users {

  // Don't do this, make a real user database instead
  private static $USERS = [
    'aaronpk' => '1234',
  ];

  public static function exists($username) {
    return array_key_exists($username, self::$USERS);
  }

  public static function password($username, $password) {
    return self::$USERS[$username] == $password;
  }
}

