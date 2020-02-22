<?php

defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);
defined('IRATE_PATH') or define('IRATE_PATH', __DIR__ . '/..');

require ROOT_PATH . '/vendor/autoload.php';
use Irate\Core\Cli\ConsoleLogger;
use Irate\Core\Cli\Migrations;
use Irate\Core\Cli\Packager;
use Irate\Core\Cli\Prompt;
use Irate\Core\Cli\Configure;
use Irate\System;

$command = isset($argv[1]) ? $argv[1] : false;
$action  = isset($argv[2]) ? $argv[2] : false;

// Make sure a command is at least given.
if (!$command) {
  ConsoleLogger::log('Command not found, exiting...');
  exit;
}

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

    ConsoleLogger::log($System::$version);
    break;

  case 'setup':
    $data = [];

    // If we are not on windows, ask to chmod.
    if (strpos(php_uname(), 'Windows') === false) {
      $chmod = Prompt::yesOrNo('To run Irate Framework, you must chmod several directories. Can we attempt this?', 'Y');

      if ($chmod) {
        foreach ($writableDirectories as $dir) {
          shell_exec('sudo chmod -R 777 ' . $dir);
          ConsoleLogger::log("$dir now writable.");
        }
      }
    }

    $data['BASE_URL'] = Prompt::input('Base URL of your application? Things like `/iratephp` or `http://localhost/iratephp`');

    $data['SETUP_DB'] = Prompt::yesOrNo('Do you want to setup your MySQL connection?', 'Y');
    if ($data['SETUP_DB']) {
      $data['DB_HOST'] = Prompt::input('Database Host', '127.0.0.1');
      $data['DB_NAME'] = Prompt::input('Database Name');
      $data['DB_USER'] = Prompt::input('Database Username');
      $data['DB_PASS'] = Prompt::input('Database Password');
    }

    $data['SHOW_ERRORS'] = Prompt::yesOrNo('Do you want to show detailed errors?', 'n');

    $data['ENCODING_KEY'] = Prompt::input('Provide an encoding key (Can be a random string, if empty we will generate it for you.)');

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
    if (!$action) {
      ConsoleLogger::log('Action not provided.');
      exit;
    }

    if ($action != 'install' && $action != 'uninstall') {
      ConsoleLogger::log('Only install & uninstall supported.');
      exit;
    }

    $package = isset($argv[3]) ? $argv[3] : false;

    if (!$package) {
      ConsoleLogger::log('No package provided.');
      exit;
    }

    // Instantiate system without the run method.
    // This will make all resources available.
    $System = new System();

    $Packager = new Packager($System);

    if ($action == 'install') {
      $Packager->install($package);
    }

    if ($action == 'uninstall') {
      $Packager->uninstall($package);
    }
    break;

  default:
    ConsoleLogger::log('Command not supported.');
    break;
}

exit;
