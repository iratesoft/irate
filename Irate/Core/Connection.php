<?php

namespace Irate\Core;

use \PDO;

class Connection {

  public $client = null;
  public $connection = false;

  public function __construct() {
    if (!\Application\Config::DB_HOST) return false;
    $this->instantiate();
  }

  /**
   * Instantiates a PDO Connection based on the
   * Application\Config DB_* variables
   */
  private function instantiate() {
    try {
      $this->client = new PDO(
          'mysql:host=' . \Application\Config::DB_HOST . ';dbname=' . \Application\Config::DB_NAME . ';charset=' . (\Application\Config::DB_CHARSET ? \Application\Config::DB_CHARSET : 'utf8mb4'),
          \Application\Config::DB_USER,
          \Application\Config::DB_PASSWORD
      );

      $this->connection = true;
    } catch (\Exception $e) {
      throw new \Exception('[MySQL Error]: ' . $e->getMessage());
    }

    return true;
  }
}
