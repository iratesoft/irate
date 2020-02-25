<?php

namespace Irate\Core;

/**
 * Utility Class
 */
class Utilities
{
  /**
   * Converts a String to StudlyCaps
   *
   * This Phrase would then become ThisPhrase
   * This-Phrase would become ThisPhrase
   */
  public static function convertToStudlyCaps($string) {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
  }

  /**
   * Simply returns a StudlyCasePhrase with the first
   * letter lowercase.
   *
   * This-Phrase becomes thisPhrase
   */
  public static function convertToCamelCase($string) {
    return lcfirst($this->convertToStudlyCaps($string));
  }
}
