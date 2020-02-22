<?php

namespace Irate\Core\Cli;

use Irate\Core\Cli\ConsoleLogger;
use Irate\Core\Cli\Migrations;
use Irate\System;

class Configure {

  // Path to the configuration files for Irate
  public $CONFIG_FILES_PATH;

  // Variables that need to be set for setup
  public $SETUP_DB = false;
  public $DB_HOST = '';
  public $DB_NAME = '';
  public $DB_USER = '';
  public $DB_PASS = '';
  public $SHOW_ERRORS = 'false';
  public $BASE_URL = false;
  public $ENCODING_KEY = false;

  // Private classes
  private $migrations;
  private $system;

  public function __construct($vars = []) {
    $this->CONFIG_FILES_PATH = IRATE_PATH . '/Resources/ConfigFiles/';

    // Set each variable passed
    foreach ($vars as $key => $var) {
      $this->$key = $var;
    }
  }

  /**
   * Runs the configuration process.
   *
   * Will write the config and htaccess
   */
  public function run() {
    $this->writeConfig();
    $this->output('Setup complete.');
  }

  /**
   * Take all data, and replace all of the
   * variables in the DefaultConfig, then move it
   * to the correct place.
   *
   * Also, if DB is setup, run the base migration.
   */
  public function writeConfig() {
    // Get the default config
    $contents = file_get_contents($this->CONFIG_FILES_PATH . 'DefaultConfig.php.copy');

    // Database
    if ($this->SETUP_DB) {
      $contents = str_replace('{DB_HOST}', $this->DB_HOST, $contents);
      $contents = str_replace('{DB_NAME}', $this->DB_NAME, $contents);
      $contents = str_replace('{DB_USER}', $this->DB_USER, $contents);
      $contents = str_replace('{DB_PASS}', $this->DB_PASS, $contents);
    }

    // Errors
    $SHOW_ERRORS = ($this->SHOW_ERRORS ? 'true' : 'false');
    $contents = str_replace('{SHOW_ERRORS}', $SHOW_ERRORS, $contents);

    if ($this->BASE_URL) {
      $contents = str_replace("{BASE_URL}", $this->BASE_URL, $contents);
    }

    if (!$this->ENCODING_KEY) {
      $contents = str_replace('{ENCODING_KEY}', md5(time()), $contents);
    } else {
      $contents = str_replace('{ENCODING_KEY}', $this->ENCODING_KEY, $contents);
    }

    // Put the new file.
    file_put_contents(ROOT_PATH . '/Application/Config.php', $contents);

    if ($this->SETUP_DB) $this->attemptBaseMigration();

    $this->output('Done writing Config.php');
  }

  /**
   * Attempts to setup an instance of Irate\System
   * and Irate\Core\Cli\Migrations to migrate to
   * base.sql.
   */
  private function attemptBaseMigration() {
    $this->setSystemInstance();
    $this->setMigrationInstance();

    try {
      $this->migrations->migrate('up', 'base');
    } catch (\Exception $e) {
      ConsoleLogger::log($e->getMessage());
    }
  }

  // Simple logging
  private function output($text) {
    ConsoleLogger::log($text);
  }

  // Sets a new instance of Irate\System
  private function setSystemInstance() {
    $this->system = new System();
  }

  // Sets a new instance of Irate\Core\Cli\Migrations
  private function setMigrationInstance() {
    $this->migrations = new Migrations($this->system);
  }
}
