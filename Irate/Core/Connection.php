<?php

namespace Irate\Core;

use \PDO;

class Connection {

  public $client = null;
  public $connection = false;
  private $config = false;

  public function __construct($vars = []) {

    if (isset($vars['config'])) {
      $this->config = $vars['config'];
    }

    if (!isset($this->config->DB_HOST) ||
        !isset($this->config->DB_NAME) ||
        !isset($this->config->DB_USER) ||
        !isset($this->config->DB_PASSWORD)) {
      \Irate\Core\Logger::log('One of the DB_ constants are missing. Skipping Database Connection.');
      return false;
    }

    if (!$this->config->DB_HOST) return false;
    if (empty($this->config->DB_HOST)) return false;
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
          'mysql:host=' . $this->config->DB_HOST . ';dbname=' . $this->config->DB_NAME . ';charset=' . (isset($this->config->DB_CHARSET) ? $this->config->DB_CHARSET : 'utf8mb4'),
          $this->config->DB_USER,
          $this->config->DB_PASSWORD
      );

      $this->connection = true;
      \Irate\Core\Logger::log('Database connection configured successfully.');
    } catch (\Exception $e) {
      throw new \Exception('[MySQL Error]: ' . $e->getMessage());
    }

    return true;
  }
}
