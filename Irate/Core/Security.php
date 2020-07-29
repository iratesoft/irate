<?php

namespace Irate\Core;

class Security {

  private $config = [];

  public $csrfField = '_csrf';
  public $csrfToken;

  public function __construct($data) {
    if (isset($data['config'])) $this->config = $data['config'];

    // Start session if not active.
    if (!$this->sessionStarted()) session_start();

    // Attempt to generate a new token.
    $this->generate();

    // Set the token
    $this->setToken();
  }

  /**
   * ============================================
   * BASE64
   * ============================================
   */

  public function encode($string) {
    return base64_encode($string . $this->config->ENCODING_KEY);
  }

  public function decode($string) {
    $decoded = base64_decode($string);
    return str_replace($this->config->ENCODING_KEY, '', $decoded);
  }


  /**
   * ============================================
   * CSRF Methods
   * ============================================
   */

  /**
   * If present on an action, it will check to see if
   * the CSRF token passes the validation based on
   * session data.
   */
  public function requireCsrf() {

    // Only test on the following request method.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $passed = $this->check();

      // If it did not pass, stop the application here.
      if (!$passed) throw new \Exception('CSRF Token is invalid.');
    }
  }

  /**
   * Checks the token provided against the session
   * and post data to confirm validation.
   */
  public function check() {

    // If it was not provided
    if (!isset($_POST[$this->csrfField])) {
      return false;
    }

    // All conditionals
    if(isset($_SESSION[$this->csrfField]) && $_POST[$this->csrfField] === $_SESSION[$this->csrfField]) {

      // Unset the current token.
      unset($_SESSION[$this->csrfField]);
      return true;
    }

    // Return false if it got here.
    return false;
  }

  /**
   * Generates a new csrf token, and sets it in session.
   */
  public function generate() {

    // If it's not already set.
    if (!isset($_SESSION[$this->csrfField])) {
      $this->csrfToken = bin2hex(random_bytes(32));
      $_SESSION[$this->csrfField] = $this->csrfToken;
    }
  }

  // Set the class variable
  public function setToken() {
    if (!isset($_SESSION[$this->csrfField])) {
      $this->csrfToken = false;
    } else {
      $this->csrfToken = $_SESSION[$this->csrfField];
    }
  }

  // Check if session is started.
  private function sessionStarted() {
    if (php_sapi_name() !== 'cli') {
      if (version_compare(phpversion(), '5.4.0', '>=') ) {
        return session_status() === PHP_SESSION_ACTIVE ? true : false;
      } else {
        return session_id() === '' ? false : true;
      }
    }
    return false;
  }
}
