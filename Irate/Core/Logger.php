<?php

namespace Irate\Core;

abstract class Logger {

  /**
   * Logs debug information to the log destination
   * below. Default: /Logs/DATE_debug.txt
   */
  public static function log($text) {
    /**
     * Configuration for log path
     * If \Application\Config::LOG_PATH is set,
     * we will base it off of that setting. If not,
     * set it to the below by default.
     */
    $logPath = ROOT_PATH . '/Logs/';
    if (defined("\Application\Config::LOG_PATH")) {
      $logPath = \Application\Config::LOG_PATH;
    }

    // Set the error log path.
    ini_set('error_log', $logPath . date('Y-m-d') . '_debug.txt');

    // If IRATE_DEBUG is set, log it.
    if (IRATE_DEBUG) error_log("[Irate\Core\Logger] " . $text);
  }
}
