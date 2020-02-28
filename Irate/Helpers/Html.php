<?php

namespace Irate\Helpers;

use Irate\System;

class Html {

  public static function span($text, $args = []) {
    $id = self::id($args);
    $classes = self::classes($args);
    $style = self::style($args);
    return "<span $style $classes $id>$text</span>";
  }

  public static function header($size = 1, $text = null, $args = []) {
    $id = self::id($args);
    $classes = self::classes($args);
    $style = self::style($args);
    return "<h$size $style $classes $id>$text</h$size>";
  }

  public static function paragraph($text, $args = []) {
    $id = self::id($args);
    $classes = self::classes($args);
    $style = self::style($args);
    return "<p $style $classes $id>$text</p>";
  }

  public static function csrfInput() {
    // Instantiate system without database connection.
    $system = new System(['db' => false]);

    // Retrieve CSRF settings for input.
    $csrfField = $system::$security->csrfField;
    $csrfToken = $system::$security->csrfToken;

    // Return hidden input
    return "<input type=\"hidden\" name=\"$csrfField\" value=\"$csrfToken\" />";
  }

  private static function id($args) {
    $id = "";
    if (isset($args['id'])) {
      $id = $args['id'];
    }
    return (empty($id) ? "" : "id=\"$id\"");
  }

  private static function classes($args) {
    $class = "";
    if (isset($args['class'])) {
      if (is_array($args['class'])) {
        $class = implode(' ', $args['class']);
      } elseif (is_string($args['class'])) {
        $class = $args['class'];
      }
    }
    return (empty($class) ? "" : "class=\"$class\"");
  }

  private static function style($args) {
    $style = "";
    if (isset($args['style'])) {
      $style = $args['style'];
    }
    return (empty($style) ? "" : "style=\"$style\"");
  }
}
