<?php

namespace Irate\Core;

abstract class Logger {

  public static function log($text) {
    if (IRATE_DEBUG) echo "[Irate\Core\Logger] " . $text . "<br />";
  }
}
