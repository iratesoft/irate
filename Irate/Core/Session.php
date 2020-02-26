<?php

namespace Irate\Core;

class Session {

  private $flashDataField = 'flash_data';
  private $flashData = [];

  private $userDataField  = 'user_data';

  public function __construct() {
    // Start session if not active.
    if (!$this->sessionStarted()) session_start();
    $this->setClassVars();
    $this->unsetFlashData();
  }

  public function destroy() {
    session_destroy();
    return true;
  }

  public function id() {
    return session_id();
  }

  /**
   * ============================================
   * USER DATA
   * ============================================
   */

  public function setUserData($key, $data = null) {
    if (!isset($_SESSION[$this->userDataField])) $_SESSION[$this->userDataField] = [];
    if (is_array($key)) $_SESSION[$this->userDataField] = $key;
    if (is_string($key)) $_SESSION[$this->userDataField][$key] = $data;
  }

  public function unsetUserData($key = null) {
    if (is_null($key) && isset($_SESSION[$this->userDataField])) unset($_SESSION[$this->userDataField]);
    if (isset($_SESSION[$this->userDataField][$key])) unset($_SESSION[$this->userDataField][$key]);
  }

  public function userData($key = null) {
    if (!isset($_SESSION[$this->userDataField])) return false;

    if (!is_null($key)) {
      return isset($_SESSION[$this->userDataField][$key]) ? $_SESSION[$this->userDataField][$key] : false;
    }

    return $_SESSION[$this->userDataField];
  }

  /**
   * ============================================
   * FLASH DATA
   * ============================================
   */

  public function getFlashData($key = null) {
    if (is_null($key)) return false;
    if (!isset($this->flashData[$key])) return false;
    return $this->flashData[$key];
  }

  public function setFlashData($key = null, $data = null) {
    if (is_null($data) || is_null($key)) return false;
    if (!isset($_SESSION[$this->flashDataField])) $_SESSION[$this->flashDataField] = [];
    $_SESSION[$this->flashDataField][$key] = $data;
  }

  public function unsetFlashData() {
    if (!isset($_SESSION[$this->flashDataField])) return false;
    unset($_SESSION[$this->flashDataField]);
    return true;
  }


  /**
   * ============================================
   * UTILITIES
   * ============================================
   */

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

  private function setClassVars() {
   if (!isset($_SESSION[$this->flashDataField])) return false;
   $this->flashData = $_SESSION[$this->flashDataField];
  }
}
