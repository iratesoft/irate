<?php

// Set default paths
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);
defined('IRATE_PATH') or define('IRATE_PATH', __DIR__ . '/..');

// Require Composer Autoloader
require ROOT_PATH . '/vendor/autoload.php';

// Package usages
use \Irate\Core\Cli\ConsoleLogger;
use Irate\Core\Cli\Migrations;
use Irate\Core\Cli\Prompt;
use Irate\Core\Cli\Generate;
use Irate\System;

// Get the command and action from CLI
$command = isset($argv[1]) ? $argv[1] : false;
$action  = isset($argv[2]) ? $argv[2] : false;

// Make sure a command is at least given.
if (!$command) {
  ConsoleLogger::log('Command not found, exiting...');
  exit;
}

// Set the directories that need to be writable.
$writableDirectories = [
  ROOT_PATH . '/Application',
  ROOT_PATH . '/Logs',
  ROOT_PATH . '/public/assets'
];

// Different command cases.
switch ($command) {

  case 'version':
    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();

    // Output the verison
    ConsoleLogger::log($System::$version);
    break;

  case 'generate':
    // Action validation
    if (!$action) {
      ConsoleLogger::log('Action not provided.');
      exit;
    }

    // Make sure a package is provided
    $name = isset($argv[3]) ? $argv[3] : false;
    if (!$name) {
      ConsoleLogger::log('No name provided.');
      exit;
    }

    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();
    $Generate = new Generate($System);

    if ($action == 'Controller') {
      $Generate->controller($name);
    } elseif ($action == 'Model') {
      $Generate->model($name);
    } elseif ($action == 'AssetBundle') {
      $Generate->assetBundle($name);
    }else {
      ConsoleLogger::log('Not supported.');
      exit;
    }
    break;

  /**
   * Quick output of the routes from the
   * config file.
   */
  case 'routes':
    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();

    // Output the ROUTES
    print_r($System->config::ROUTES);
    break;

  /**
   * Database migration manager.
   *
   * php ir8 migrate base
   * php ir8 migrate up
   * php ir8 migrate down
   */
  case 'migrate':
    if (!$action) {
      ConsoleLogger::log('Action not provided.');
      exit;
    }

    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();

    // Run the migration based on input given.
    $name = isset($argv[3]) ? $argv[3] : null;
    $MigrationsManager = new Migrations($System);
    $MigrationsManager->migrate($action, $name);
    break;

  default:
    ConsoleLogger::log('Command not supported.');
    break;
}

exit;
