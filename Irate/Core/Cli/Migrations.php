<?php

namespace Irate\Core\Cli;

use Irate\Core\Cli\ConsoleLogger;

class Migrations {

  // Statuses used for migrations
  const VERSION_STATUS_UNKNOWN  = 'unknown';
  const VERSION_STATUS_COMPLETE = 'complete';

  // Migrations path, set on construct
  private $MIGRATIONS_PATH = '';

  // Database name, set on __construct
  private $DATABASE_NAME = '';

  // Migration table name
  private $migrationTable = 'migration';

  /**
   * Check for ROOT_PATH, then setup the rest
   * of the class resources.
   */
  public function __construct($system) {
    if (!defined('ROOT_PATH')) {
      throw new \Exception('ROOT_PATH is not defined.');
    }

    $this->system = $system;
    $this->setMigrationsPath();
    $this->checkConnection();
    $this->setDatabaseVariables();
  }

  /**
   * Runs specific migration up file if available.
   */
  public function migrateUp($name) {
    $this->createMigrationTable();
    $exists = $this->migrationExists($name);
    if ($exists) throw new \Exception('Migration already exists.');
    $sql = $this->getMigrationFile($name, 'up');
    if (!$sql) throw new \Exception("Migration File [$name.sql] does not exist.");

    // Try to run the query.
    try {
      $this->system::$db->client->query($sql);
    } catch (\Exception $e) {
      ConsoleLogger::log('MigrationUp[' . $name . '] has failed. ' . $e->getMessage());
      return false;
    }

    // Insert the migration status.
    $this->insertMigrationStatus($name);
  }

  /**
   * Runs specific migration down file if available.
   */
  public function migrateDown($name) {
    $this->createMigrationTable();
    $exists = $this->migrationExists($name);
    if (!$exists) throw new \Exception('Migration does not exist in the migration log.');
    $sql = $this->getMigrationFile($name, 'down');
    if (!$sql) throw new \Exception("Migration File [$name.sql] does not exist.");

    // Try to run the query.
    try {
      $this->system::$db->client->query($sql);
    } catch (\Exception $e) {
      ConsoleLogger::log('MigrationDown[' . $name . '] has failed. ' . $e->getMessage());
      return false;
    }

    // Insert the migration status.
    $this->removeMigrationStatus($name);
  }

  /**
   * Runs all available up or down migration files.
   */
  public function all($direction) {
    ConsoleLogger::log('Migrating all ' . $direction);
    $this->createMigrationTable();
    $migrations = $this->migrationFiles($direction);

    foreach ($migrations as $migration) {
      $file = basename($migration);
      $name = str_replace('.sql', '', $file);

      // Gets the base sql file contents.
      $sql = $this->getMigrationFile($name, $direction);

      // If it does not exist
      if (!$sql) throw new \Exception("Migration File [$name.sql] does not exist.");

      // Try to run the query.
      try {
        $this->system::$db->client->query($sql);
      } catch (\Exception $e) {
        ConsoleLogger::log('Migration[' . $name . '] has failed. ' . $e->getMessage());
        return false;
      }

      if ($direction == 'up') {
        $this->insertMigrationStatus($name);
      } else {
        $this->removeMigrationStatus($name);
      }
    }
  }

  /**
   * Removes tables, recreates migration table.
   */
  public function reset() {
    $this->migrate('all', 'down');
    ConsoleLogger::log('Migrations have been reset.');
  }

  /**
   * Runs certain methods based on the type of migration
   * requested.
   */
  public function migrate($type = 'all', $name = null) {

    switch ($type) {

      case 'reset':
        $this->reset();
        break;

      // Run the base migration
      case 'all':
        if (is_null($name)) {
          throw new \Exception('Direction can not be null.');
        }

        $this->all($name); // Up or down
        break;

      // Run the up migration
      case 'up':
        if (is_null($name)) {
          throw new \Exception('Migration name can not be null.');
        }

        $this->migrateUp($name);
        break;

      // Run the down migration
      case 'down':
        if (is_null($name)) {
          throw new \Exception('Migraiton name can not be null.');
        }

        $this->migrateDown($name);
        break;
    }
  }

  // Set the migration directory path
  private function setMigrationsPath() {
    $this->MIGRATIONS_PATH = ROOT_PATH . '/Application/Migrations/';
    ConsoleLogger::log('MIGRATIONS_PATH[' . $this->MIGRATIONS_PATH . ']');
  }

  /**
   * Checks the MYSQL Connection to make sure
   * we have a good connection. If not, we need to throw
   * an exception here.
   */
  private function checkConnection() {
    if (!$this->system::$db->connection) {
      throw new \Exception('Database connection is no good.');
    }

    ConsoleLogger::log('Database connection is good.');
  }

  /**
   * Set the necessary database variables from the
   * Application\Config class.
   */
  private function setDatabaseVariables() {
    $this->DATABASE_NAME = $this->system->config::DB_NAME;
    ConsoleLogger::log("Database Name: $this->DATABASE_NAME");
  }

  /**
   * Create the migration table so we can start
   * recording the migrations ran.
   */
  private function createMigrationTable() {
    $this->system::$db->client->query('CREATE TABLE IF NOT EXISTS ' . $this->migrationTable . ' (version VARCHAR(255), status VARCHAR(20))');
  }

  /**
   * Inserts a migration status into the migration
   * table after a migration has completed.
   */
  private function insertMigrationStatus($version) {
    $now = time();
    $status = Migrations::VERSION_STATUS_COMPLETE;

    try {
      $sql = "INSERT INTO " . $this->migrationTable . "(version, status) VALUES(:version, :status)";
      $statement = $this->system::$db->client->prepare($sql);
      $statement->bindParam(':version', $version);
      $statement->bindParam(':status', $status);
      $statement->execute();
    } catch (\Exception $e) {
      ConsoleLogger::log("Migration[$version] Failed. " . $e->getMessage());
      return false;
    }

    ConsoleLogger::log("Migration[$version] Complete");
    return true;
  }

  private function removeMigrationStatus($version) {
    $now = time();

    try {
      $sql = "DELETE FROM " . $this->migrationTable . " WHERE version = :version";
      $statement = $this->system::$db->client->prepare($sql);
      $statement->bindParam(':version', $version);
      $statement->execute();
    } catch (\Exception $e) {
      ConsoleLogger::log("Removal of migration[$version] Failed. " . $e->getMessage());
      return false;
    }

    ConsoleLogger::log("Migration[$version] removed");
    return true;
  }

  /**
   * Deletes all existing tables in the database.
   * Usually happens when a reset has been requested.
   */
  private function deleteCurrentTables() {
    $this->system::$db->client->query('SET foreign_key_checks = 0');
    $tables = $this->getAllTables();
    $resultKey = 'Tables_in_' . $this->DATABASE_NAME;

    foreach ($tables as $table) {
      ConsoleLogger::log("DROP TABLE IF EXISTS " . $table[$resultKey]);
      $this->system::$db->client->query('DROP TABLE IF EXISTS ' . $table[$resultKey]);
    }

    $this->system::$db->client->query('SET foreign_key_checks = 1');
  }

  /**
   * Gets a list of all current tables.
   */
  private function getAllTables() {
    $sql = "SHOW TABLES FROM " . $this->DATABASE_NAME;
    $statement = $this->system::$db->client->prepare($sql);
    $statement->execute();
    $tables = $statement->fetchAll(\PDO::FETCH_ASSOC);
    return $tables;
  }

  /**
   * Return migration files if they are available.
   */
  private function migrationFiles($direction) {
    $filePattern = $this->MIGRATIONS_PATH . ($direction) . '/*.sql';

    $result = array_filter(glob($filePattern), function ($file) use ($direction) {
      $exists = $this->migrationExists(str_replace('.sql', '', basename($file)));

      if ($direction == 'up' && !$exists) {
        return true;
      } elseif ($direction == 'down' && $exists) {
        return true;
      } else {
        return false;
      }
    });

    if (count($result) === 0) {
      ConsoleLogger::log('No migrations available to run.');
    }

    return $result;
  }

  /**
   * Check if a migration already exists in the migration table log.
   */
  private function migrationExists($name) {
    $sql = "SELECT * FROM " . $this->migrationTable. " WHERE version = :version";
    $statement = $this->system::$db->client->prepare($sql);
    $statement->bindParam(':version', $name);
    $statement->execute();
    $data = $statement->fetch(\PDO::FETCH_ASSOC);

    if (!$data) return false;
    return true;
  }

  /**
   * Grabs the contents of a specific migration file.
   */
  private function getMigrationFile($name, $direction = null) {
    $fileName = $this->MIGRATIONS_PATH . ($direction ? $direction . '/' : '') . $name . '.sql';

    if (!file_exists($fileName)) {
      return false;
    }

    $contents = file_get_contents($fileName);
    return $contents;
  }
}
