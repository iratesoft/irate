<?php

namespace Irate\Core\Cli;

class ConsoleLogger {

  /**
   * Simple CLI logging method
   *
   * @TODO: Add console colors based on log level.
   */
  public static function log($text) {
    echo "[Cli\ConsoleLogger] " . $text . PHP_EOL;
  }
}
