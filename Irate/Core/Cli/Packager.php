<?php

namespace Irate\Core\Cli;

use Irate\Core\Cli\ConsoleLogger;
use Irate\Core\Cli\Migrations;

class Packager {

  private $system;
  private $migrations;
  private $packagesDirectory = false;
  private $applicationDirectory = false;

  public function __construct($system) {
    $this->system = $system;
    $this->setPackagesDirectory();
    $this->setApplicationDirectory();
    $this->setMigrationInstance();
    ConsoleLogger::log('Packager constructed successfully.');
  }

  public function install($package = null) {
    if (is_null($package)) {
      throw new \Exception('Package not found.');
    }

    $packageInfo = $this->information($package);
    ConsoleLogger::log('Package verified. Continuing with install.');

    $packageName = $packageInfo['install']['name'];
    $directories = $packageInfo['install']['directories'];

    $count = 0;

    $migrationUp = false;

    foreach ($directories as $directory => $files) {
      $toDirectory = $this->applicationDirectory . '/' . $directory;

      foreach ($files as $file) {
        $toFile = $toDirectory . '/' . basename($file);

        if ($directory == 'Migrations' && basename($file) == 'up.sql') {
          $toFile = $toDirectory . '/up/package_' . $packageName . '.sql';
          $migrationUp = 'package_' . $packageName;
        } elseif ($directory == 'Migrations' && basename($file) == 'down.sql') {
          $toFile = $toDirectory . '/down/package_' . $packageName . '.sql';
        } elseif ($directory == 'Views') {
          if (!is_dir($toDirectory . '/' . strtolower($packageName))) mkdir($toDirectory . '/' . strtolower($packageName));
          $toFile = $toDirectory . '/' . strtolower($packageName) . '/' . basename($file);
        }
        $this->copy($file, $toFile);
        $count++;
      }
    }

    if (isset($packageInfo['install']['assets'])) {
      if (isset($packageInfo['install']['assets']['scripts'])) {
        foreach ($packageInfo['install']['assets']['scripts'] as $scriptFile) {
          $toFile = ROOT_PATH . '/public/assets/scripts/' . basename($scriptFile);
          $this->copy($scriptFile, $toFile);
          $count++;
        }
      }

      if (isset($packageInfo['install']['assets']['css'])) {
        foreach ($packageInfo['install']['assets']['css'] as $cssFile) {
          $toFile = ROOT_PATH . '/public/assets/css/' . basename($cssFile);
          $this->copy($cssFile, $toFile);
          $count++;
        }
      }
    }

    ConsoleLogger::log("Files copied: $count");

    if (isset($packageInfo['install']['routes'])) {
      $configFile = ROOT_PATH . '/Application/Config.php';
      $config = file_get_contents($configFile);

      $routesString = '';

      foreach ($packageInfo['install']['routes'] as $route) {
        $routesString .= "$route\r\n";
      }

      $config = str_replace('ROUTES = [', "ROUTES = [\r\n$routesString", $config);
      file_put_contents($configFile, $config);

      ConsoleLogger::log('Done updating routes.');
    }

    if ($migrationUp) {
      $this->migrations->migrate('up', $migrationUp);
    }

    ConsoleLogger::log("Installation complete.");
  }

  public function uninstall($package = null) {
    if (is_null($package)) {
      throw new \Exception('Package not found.');
    }

    $packageInfo = $this->information($package);
    ConsoleLogger::log('Package verified. Continuing with uninstall.');

    $packageName = $packageInfo['install']['name'];
    $directories = $packageInfo['install']['directories'];

    $count = 0;

    $migrationDown = false;

    foreach ($directories as $directory => $files) {
      $toDirectory = $this->applicationDirectory . '/' . $directory;

      foreach ($files as $file) {
        $fileToRemove = $toDirectory . '/' . basename($file);

        if ($directory == 'Migrations' && basename($file) == 'up.sql') {
          $fileToRemove = $toDirectory . '/up/package_' . $packageName . '.sql';
        } elseif ($directory == 'Migrations' && basename($file) == 'down.sql') {
          $fileToRemove = $toDirectory . '/down/package_' . $packageName . '.sql';
          $migrationDown = 'package_' . $packageName;

          // Must migrate down before it's deleted.
          $this->migrations->migrate('down', $migrationDown);
        } elseif ($directory == 'Views') {
          $fileToRemove = $toDirectory . '/' . strtolower($packageName) . '/' . basename($file);
        }

        $this->removeFIle($fileToRemove);
        $count++;
      }
    }

    if (isset($packageInfo['install']['assets'])) {
      if (isset($packageInfo['install']['assets']['scripts'])) {
        foreach ($packageInfo['install']['assets']['scripts'] as $scriptFile) {
          $fileToRemove = ROOT_PATH . '/public/assets/scripts/' . basename($scriptFile);
          $this->removeFIle($fileToRemove);
          $count++;
        }
      }

      if (isset($packageInfo['install']['assets']['css'])) {
        foreach ($packageInfo['install']['assets']['css'] as $cssFile) {
          $fileToRemove = ROOT_PATH . '/public/assets/css/' . basename($cssFile);
          $this->removeFIle($fileToRemove);
          $count++;
        }
      }
    }

    ConsoleLogger::log("Files removed: $count");

    if (isset($packageInfo['install']['routes'])) {
      $configFile = ROOT_PATH . '/Application/Config.php';
      $config = file_get_contents($configFile);

      foreach ($packageInfo['install']['routes'] as $route) {
        $config = str_replace($route, '', $config);
      }

      file_put_contents($configFile, $config);

      ConsoleLogger::log('Done removing routes.');
    }

    ConsoleLogger::log("Uninstall complete.");
  }

  private function setMigrationInstance() {
    $this->migrations = new Migrations($this->system);
  }

  private function setPackagesDirectory() {
    $this->packagesDirectory = ROOT_PATH . '/Irate/Resources/Packages';
  }

  private function setApplicationDirectory() {
    $this->applicationDirectory = ROOT_PATH . '/Application';
  }

  private function copy($from, $to) {
    copy($from, $to);
  }

  private function removeFile($file) {
    try {
      unlink($file);
    } catch (\Exception $e) {
      ConsoleLogger::log("Unable to find $file");
    }
  }

  private function information($package) {
    $directory = $this->packagesDirectory . '/' . $package;
    $installer = $directory . '/package.installer.php';

    if (!is_dir($directory)) {
      throw new \Exception("$directory does not exist.");
    }

    if (!file_exists($installer)) {
      throw new \Exception("$installer does not exist.");
    }

    $installInfo = require $installer;
    return [
      'directory' => $directory,
      'install'   => $installInfo
    ];
  }
}
