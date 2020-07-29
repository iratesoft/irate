<?php

// Set default paths
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);
defined('IRATE_PATH') or define('IRATE_PATH', __DIR__ . '/..');

// Require Composer Autoloader
require ROOT_PATH . '/vendor/autoload.php';

// Package usages
use \Irate\Core\Cli\ConsoleLogger;
use Irate\Core\Cli\Migrations;
use Irate\Core\Cli\Packager;
use Irate\Core\Cli\Prompt;
use Irate\Core\Cli\Configure;
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

  case 'setup':
    $data = [];

    // If we are not on windows, ask to chmod.
    if (strpos(php_uname(), 'Windows') === false) {
      $chmod = Prompt::yesOrNo('To run Irate Framework, you must chmod several directories. Can we attempt this?', 'Y');

      // If They want to CHMOD, let's do it for each directory.
      if ($chmod) {
        foreach ($writableDirectories as $dir) {
          shell_exec('sudo chmod -R 777 ' . $dir);
          ConsoleLogger::log("$dir now writable.");
        }
      }
    }

    // Get the base URL of the Application
    $data['BASE_URL'] = Prompt::input('Base URL of your application? Things like `/iratephp` or `http://localhost/iratephp`');

    // Give them option to setup PDO
    $data['SETUP_DB'] = Prompt::yesOrNo('Do you want to setup your MySQL connection?', 'Y');

    // If they do
    if ($data['SETUP_DB']) {

      // Ask them for all of the database variables.
      $data['DB_HOST'] = Prompt::input('Database Host', '127.0.0.1');
      $data['DB_NAME'] = Prompt::input('Database Name');
      $data['DB_USER'] = Prompt::input('Database Username');
      $data['DB_PASS'] = Prompt::input('Database Password');
    }

    // Set the SHOW_ERRORS config variable
    $data['SHOW_ERRORS'] = Prompt::yesOrNo('Do you want to show detailed errors?', 'n');

    // Ask to generate the ENCODING_KEY (Or generate a random one)
    $data['ENCODING_KEY'] = Prompt::input('Provide an encoding key (Can be a random string, if empty we will generate it for you.)');

    // Instantiate the configure class, then run it.
    $Configure = new Configure($data);
    $Configure->run();
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

  /**
   * Irate Framework package manager.
   *
   * php ir8 package install user
   * php ir8 package uninstall user
   */
  case 'package':
    // Action validation
    if (!$action) {
      ConsoleLogger::log('Action not provided.');
      exit;
    }

    // More action validation
    if ($action != 'install' && $action != 'uninstall') {
      ConsoleLogger::log('Only install & uninstall supported.');
      exit;
    }

    // Make sure a package is provided
    $package = isset($argv[3]) ? $argv[3] : false;
    if (!$package) {
      ConsoleLogger::log('No package provided.');
      exit;
    }

    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();

    // Instantiate
    $Packager = new Packager($System);

    // Run install or uninstall.
    if ($action == 'install') $Packager->install($package);
    if ($action == 'uninstall') $Packager->uninstall($package);
    break;

  default:
    ConsoleLogger::log('Command not supported.');
    break;
}

exit;
