<?php

namespace Irate\Core;

use \Smarty;

define("HTML_MINIFY_URL_ENABLED", false);

include __DIR__ . '/Smarty/HTMLMinify.smarty.php';

/**
 * View
 *
 * @TODO Add renderTemplate method.
 */
class View
{
  private static $Smarty;
  private static $baseUrl;
  private static $system;
  private static $config;

  // Class constructor
  public function __construct($vars = []) {
    if (isset($vars['system'])) {
      self::$system = $vars['system'];
    }

    if (isset($vars['baseUrl'])) {
      self::$baseUrl = $vars['baseUrl'];
    }

    if (isset($vars['config'])) {
      self::$config = $vars['config'];
    }

    self::instantiateSmarty();
  }

  public static function instantiateSmarty() {
    self::$Smarty = new Smarty;

    // Smarty configurations
    self::$Smarty->template_dir  = ROOT_PATH . '/Application/Views';
    self::$Smarty->cache_dir     = ROOT_PATH . '/Application/Cache';
    self::$Smarty->compile_dir   = ROOT_PATH . '/Application/ViewsCompiled';
    self::$Smarty->force_compile = false;

    self::$Smarty->registerFilter("output", "minify_html");
  }

  // Render a template
  public static function renderTemplate($template, $args = []) {

    // Add each argument passed to the smarty variables.
    foreach ($args as $key => $value) {
      self::$Smarty->assign($key, $value);
    }

    // Setup urls
    $baseUrl = self::$baseUrl;
    $assetsUrl = (substr($baseUrl, -1) === '/' ? $baseUrl . 'assets' : $baseUrl . '/assets');

    // All variables to use in templates
    self::$Smarty->assign('baseUrl',   $baseUrl);
    self::$Smarty->assign('assetsUrl', $assetsUrl);
    self::$Smarty->assign('app',       self::$system);
    self::$Smarty->assign('asset',     self::$system::$AssetBundle);
    self::$Smarty->assign('security',  self::$system::$security);
    self::$Smarty->assign('session',   self::$system::$session);
    self::$Smarty->assign('libraries', self::$system::$libraries);

    // Register HTML Helper
    self::$Smarty->registerClass("Html", "\\Irate\\Helpers\\Html");

    // Display the template file.
    self::$Smarty->display("$template.tpl");
  }

  public static function renderEmail($template, $args = []) {
    // Add each argument passed to the smarty variables.
    foreach ($args as $key => $value) {
      self::$Smarty->assign($key, $value);
    }

    // Display the template file.
    return self::$Smarty->fetch("emails/$template.tpl");
  }
}
