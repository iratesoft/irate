<?php

namespace Irate\Core;

use \PDO;

class Connection {

  public $client = null;
  public $connection = false;

  public function __construct() {
    if (!defined('\Application\Config::DB_HOST') ||
        !defined('\Application\Config::DB_NAME') ||
        !defined('\Application\Config::DB_USER') ||
        !defined('\Application\Config::DB_PASSWORD')) {
      \Irate\Core\Logger::log('One of the DB_ constants are missing. Skipping Database Connection.');
      return false;
    }

    if (!\Application\Config::DB_HOST) return false;
    if (empty(\Application\Config::DB_HOST)) return false;
    $this->instantiate();
  }

  /**
   * Instantiates a PDO Connection based on the
   * Application\Config DB_* variables
   */
  private function instantiate() {
    \Irate\Core\Logger::log('Instantiating PDO Connection.');

    try {
      $this->client = new PDO(
          'mysql:host=' . \Application\Config::DB_HOST . ';dbname=' . \Application\Config::DB_NAME . ';charset=' . (defined('\Application\Config::DB_PASSWORD') ? \Application\Config::DB_CHARSET : 'utf8mb4'),
          \Application\Config::DB_USER,
          \Application\Config::DB_PASSWORD
      );

      $this->connection = true;
      \Irate\Core\Logger::log('Database connection configured successfully.');
    } catch (\Exception $e) {
      throw new \Exception('[MySQL Error]: ' . $e->getMessage());
    }

    return true;
  }
}
