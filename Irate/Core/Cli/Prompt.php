<?php

namespace Irate\Core\Cli;

class Prompt {

  /**
   * Ability to ask a yes or no question via PHP CLI.
   * Acceptable answers:
   * Y/y/N/n (Enter accepts default if one is provided)
   */
  public static function yesOrNo($question, $default = null) {
    self::output($question . ' (' . ($default ? $default : 'n') . ')');
    $handle = fopen ("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (strtoupper(trim($line)) === 'YES' ||
        strtoupper(trim($line)) === 'Y') {
      return true;
    } elseif (strtoupper(trim($line)) === 'NO' ||
              strtoupper(trim($line)) === 'N') {
      return false;
    } elseif (empty(trim($line))) {
      return ($default === 'Y' || $default === 'y' ? true : false);
    } else {
      return self::yesOrNo($question, $default);
    }
  }

  /**
   * Ability to accept a custom input to a command line question.
   * You can provide default that is accepted on enter.
   */
  public static function input($question, $default = null) {
    self::output($question . ($default ? ' (' . $default . ')' : ''));
    $handle = fopen ("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    $res = trim($line);

    if (empty($res)) {
      return $default;
    }

    return $res;
  }

  private static function output($text) {
    echo $text . PHP_EOL;
  }
}
