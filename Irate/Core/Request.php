<?php

namespace Irate\Core;

class Request {

  /**
   * Requires a specific REQUEST_METHOD for an
   * action.
   */
  public function requireMethod($method = 'get') {
    $method = strtoupper($method);

    if ($_SERVER['REQUEST_METHOD'] !== $method) {
      throw new \Exception('Required request method does not match.');
    }
  }

  /**
   * Require a GET or POST variable.
   */
  public function require($type = 'get', $var = null) {
    if (is_null($var)) return false;

    if (is_array($var)) {
      foreach ($var as $v) {
        if (!$this->$type($v)) {
          throw new \Exception('Required parameter not provided.');
        }
      }
    } else {
      if (!$this->$type($var)) {
        throw new \Exception('Required parameter not provided.');
      }
    }
  }

  // Retrieve get variables
  public function get($key = null) {
    if (is_null($key)) return $_GET;
    if (isset($_GET[$key])) {
      return $_GET[$key];
    } else {
      return false;
    }
  }

  // Retrieve POST variables
  public function post($key = null) {
    if (is_null($key)) return $_POST;
    if (isset($_POST[$key])) {
      return $_POST[$key];
    } else {
      return false;
    }
  }
}
