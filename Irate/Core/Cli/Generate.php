<?php

namespace Irate\Core\Cli;

use Irate\System;
use Irate\Core\Utilities;

class Generate {

  // Path to the configuration files for Irate
  public $CONFIG_FILES_PATH;

  // Private classes
  private $system;

  public function __construct($vars = []) {
    $this->CONFIG_FILES_PATH = IRATE_PATH . '/Resources/CopyFiles/';

    // Set each variable passed
    foreach ($vars as $key => $var) {
      $this->$key = $var;
    }
  }

  public function controller($name = null) {
    if (is_null($name)) throw new \Exception('Need a name.');
    $name = Utilities::convertToStudlyCaps($name);
    $contents = file_get_contents($this->CONFIG_FILES_PATH . 'Controller.php.copy');
    $contents = str_replace('{CONTROLLER_NAME}', $name, $contents);
    file_put_contents(ROOT_PATH . '/Application/Controllers/' . $name . '.php', $contents);
    $this->output($name . ' created in /Application/Controllers/' . $name . '.php');
    return true;
  }

  public function model($name = null) {
    if (is_null($name)) throw new \Exception('Need a name.');
    $name = Utilities::convertToStudlyCaps($name . 'Model');
    $contents = file_get_contents($this->CONFIG_FILES_PATH . 'Model.php.copy');
    $contents = str_replace('{MODEL_NAME}', $name, $contents);
    file_put_contents(ROOT_PATH . '/Application/Models/' . $name . '.php', $contents);
    $this->output($name . ' created in /Application/Models/' . $name . '.php');
    return true;
  }

  public function assetBundle($name = null) {
    if (is_null($name)) throw new \Exception('Need a name.');
    $name = Utilities::convertToStudlyCaps($name . 'AssetBundle');
    $contents = file_get_contents($this->CONFIG_FILES_PATH . 'AssetBundle.php.copy');
    $contents = str_replace('{ASSET_BUNDLE_NAME}', $name, $contents);
    file_put_contents(ROOT_PATH . '/Application/Assets/' . $name . '.php', $contents);
    $this->output($name . ' created in /Application/Assets/' . $name . '.php');
    return true;
  }

  // Simple logging
  private function output($text) {
    ConsoleLogger::log($text);
  }
}
