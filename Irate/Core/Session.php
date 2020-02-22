<?php

namespace Irate\Core;

class Session {

  private $flashDataField = 'flash_data';
  private $flashData = [];

  public function __construct() {
    // Start session if not active.
    if (!$this->sessionStarted()) session_start();
    $this->set();
    $this->unsetFlashData();
  }

  private function set() {
    if (!isset($_SESSION[$this->flashDataField])) return false;
    $this->flashData = $_SESSION[$this->flashDataField];
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
}
